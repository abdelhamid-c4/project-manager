<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * AI-ready service for generating task suggestions from a project description.
 *
 * Set GROQ_API_KEY in your .env to enable Groq Llama reports.
 * The key is read from config/services.php — never hardcoded here.
 */
class AiProjectService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.groq.base_url', 'https://api.groq.com/openai/v1'), '/');
        $this->apiKey = (string) config('services.groq.key', '');
        $this->model = (string) config('services.groq.model', 'llama-3.3-70b-versatile');
    }

    /**
     * Generate a list of suggested tasks for a given project description.
     *
     * @param  string  $projectDescription
     * @param  int     $count  Number of tasks to suggest
     * @return array   [ ['title' => ..., 'description' => ..., 'priority' => ...], ... ]
     *
     * @throws \RuntimeException if the API key is missing or the call fails
     */
    public function suggestTasks(string $projectDescription, int $count = 5): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('Groq API key is not configured.');
        }

        $prompt = <<<PROMPT
You are a project management assistant. Based on the following project description,
suggest {$count} concrete tasks that the team should complete.

Project description:
{$projectDescription}

Respond with a valid JSON array only. Each item must have:
- title (string, max 80 chars)
- description (string, max 200 chars)
- priority ("low", "medium", or "high")

Example format:
[
  {"title": "Define requirements", "description": "Gather and document all project requirements.", "priority": "high"},
  ...
]
PROMPT;

        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post($this->chatCompletionsUrl(), [
                'model'    => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Groq API request failed: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content', '[]');

        // Strip markdown code fences if present
        $content = preg_replace('/```json|```/', '', $content);

        return json_decode(trim($content), true) ?? [];
    }

    /**
     * Generate a focused work report for one team member.
     */
    public function generateTeamMemberReport(User $member): array
    {
        $tasks = Task::query()
            ->with(['project', 'subtasks', 'dependencies.dependsOnTask'])
            ->where('assigned_to', $member->id)
            ->where('status', '!=', 'done')
            ->orderByRaw("CASE priority WHEN 'high' THEN 0 WHEN 'medium' THEN 1 ELSE 2 END")
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->latest()
            ->get();

        $context = $tasks->map(fn (Task $task) => $this->taskContext($task))->values();

        if ($context->isEmpty()) {
            return $this->buildFallbackTeamMemberReport($member, $context, 'local');
        }

        if (empty($this->apiKey)) {
            return $this->buildFallbackTeamMemberReport(
                $member,
                $context,
                'local',
                'Groq API key is not configured, so this report was created from task priority, status, and due dates.'
            );
        }

        try {
            $report = $this->requestTeamMemberReport($member, $context);

            return $this->normalizeTeamMemberReport($report, $context);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->buildFallbackTeamMemberReport(
                $member,
                $context,
                'fallback',
                'AI report generation failed, so this report was created from task priority, status, and due dates.'
            );
        }
    }

    protected function requestTeamMemberReport(User $member, Collection $context): array
    {
        $taskJson = json_encode(
            $context->take(12)->values(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );

        $prompt = <<<PROMPT
Create a concise work report for {$member->name}. Use only the provided task data.

Tasks:
{$taskJson}

Return a valid JSON object only with this shape:
{
  "summary": "one sentence",
  "focus": "one sentence naming the best next focus",
  "next_tasks": [
    {
      "task_id": 123,
      "urgency": "high|medium|low",
      "why": "short reason",
      "recommended_action": "specific next action"
    }
  ],
  "blockers": ["short blocker or risk"]
}

Rules:
- Pick at most 5 next_tasks.
- Do not invent tasks or task IDs.
- Prefer overdue, high-priority, due-soon, in-progress, and dependency-unblocked tasks.
- If a task appears blocked_by another open task, mention that as a blocker instead of recommending it first.
PROMPT;

        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post($this->chatCompletionsUrl(), [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a pragmatic project delivery agent running on Groq Llama. Produce compact valid JSON for a project management dashboard.',
                    ],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.2,
                'response_format' => ['type' => 'json_object'],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Groq API request failed: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content', '{}');
        $content = preg_replace('/```json|```/', '', $content);

        $decoded = json_decode(trim($content), true);

        if (!is_array($decoded)) {
            throw new \RuntimeException('Groq returned an invalid team member report.');
        }

        return $decoded;
    }

    protected function chatCompletionsUrl(): string
    {
        return $this->baseUrl . '/chat/completions';
    }

    protected function normalizeTeamMemberReport(array $report, Collection $context): array
    {
        $taskLookup = $context->keyBy('id');

        $recommendedTasks = collect($report['next_tasks'] ?? $report['tasks'] ?? [])
            ->map(function (array $item) use ($taskLookup) {
                $taskId = (int) ($item['task_id'] ?? $item['id'] ?? 0);

                if (!$taskLookup->has($taskId)) {
                    return null;
                }

                $task = $taskLookup->get($taskId);
                $urgency = $this->normalizeUrgency($item['urgency'] ?? null, $task);

                return [
                    ...$task,
                    'urgency' => $urgency,
                    'why' => $this->cleanText($item['why'] ?? $item['reason'] ?? $this->taskReason($task), 220),
                    'recommended_action' => $this->cleanText(
                        $item['recommended_action'] ?? $item['action'] ?? $this->taskAction($task),
                        220
                    ),
                ];
            })
            ->filter()
            ->take(5)
            ->values();

        if ($recommendedTasks->isEmpty()) {
            $recommendedTasks = $context
                ->take(5)
                ->map(fn (array $task) => [
                    ...$task,
                    'urgency' => $this->taskUrgency($task),
                    'why' => $this->taskReason($task),
                    'recommended_action' => $this->taskAction($task),
                ])
                ->values();
        }

        return [
            'summary' => $this->cleanText($report['summary'] ?? $this->summaryFromContext($context), 260),
            'focus' => $this->cleanText($report['focus'] ?? $this->focusFromTasks($recommendedTasks), 260),
            'tasks' => $recommendedTasks->all(),
            'blockers' => $this->normalizeBlockers($report['blockers'] ?? [], $context),
            'source' => 'groq',
            'provider' => 'groq',
            'model' => $this->model,
            'notice' => null,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    protected function buildFallbackTeamMemberReport(
        User $member,
        Collection $context,
        string $source,
        ?string $notice = null
    ): array {
        if ($context->isEmpty()) {
            return [
                'summary' => "No open tasks are currently assigned to {$member->name}.",
                'focus' => 'You are clear on assigned work. Check recent project updates or ask for the next priority.',
                'tasks' => [],
                'blockers' => [],
                'source' => $source,
                'provider' => 'local',
                'model' => null,
                'notice' => $notice,
                'generated_at' => now()->toIso8601String(),
            ];
        }

        $tasks = $context
            ->sortBy(fn (array $task) => $this->taskPriorityScore($task))
            ->take(5)
            ->map(fn (array $task) => [
                ...$task,
                'urgency' => $this->taskUrgency($task),
                'why' => $this->taskReason($task),
                'recommended_action' => $this->taskAction($task),
            ])
            ->values();

        return [
            'summary' => $this->summaryFromContext($context),
            'focus' => $this->focusFromTasks($tasks),
            'tasks' => $tasks->all(),
            'blockers' => $this->fallbackBlockers($context),
            'source' => $source,
            'provider' => 'local',
            'model' => null,
            'notice' => $notice,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    protected function taskContext(Task $task): array
    {
        $dueDate = $task->due_date;
        $blockedBy = $task->dependencies
            ->filter(fn ($dependency) => $dependency->dependsOnTask && $dependency->dependsOnTask->status !== 'done')
            ->map(fn ($dependency) => [
                'id' => $dependency->dependsOnTask->id,
                'title' => $dependency->dependsOnTask->title,
                'status' => $dependency->dependsOnTask->status,
            ])
            ->values();

        return [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $this->cleanText($task->description, 260),
            'project' => $task->project?->name ?? 'No project',
            'status' => $task->status,
            'priority' => $task->priority,
            'due_date' => $dueDate?->toDateString(),
            'days_until_due' => $dueDate ? (int) today()->diffInDays($dueDate, false) : null,
            'is_overdue' => $task->isOverdue(),
            'incomplete_subtasks' => $task->subtasks->where('is_completed', false)->count(),
            'blocked_by' => $blockedBy,
            'is_blocked' => $blockedBy->isNotEmpty(),
        ];
    }

    protected function summaryFromContext(Collection $context): string
    {
        $total = $context->count();
        $overdue = $context->where('is_overdue', true)->count();
        $highPriority = $context->where('priority', 'high')->count();

        return "You have {$total} open assigned task(s), including {$overdue} overdue and {$highPriority} high-priority item(s).";
    }

    protected function focusFromTasks(Collection $tasks): string
    {
        $first = $tasks->first();

        if (!$first) {
            return 'No immediate task focus is required.';
        }

        return "Start with \"{$first['title']}\" in {$first['project']}.";
    }

    protected function normalizeBlockers(array $blockers, Collection $context): array
    {
        $normalized = collect($blockers)
            ->map(fn ($blocker) => $this->cleanText($blocker, 180))
            ->filter()
            ->take(4)
            ->values()
            ->all();

        return $normalized ?: $this->fallbackBlockers($context);
    }

    protected function fallbackBlockers(Collection $context): array
    {
        $blockers = [];

        $blockedTasks = $context->where('is_blocked', true);
        if ($blockedTasks->isNotEmpty()) {
            $blockers[] = $blockedTasks->count() . ' task(s) are waiting on unfinished dependencies.';
        }

        $overdueTasks = $context->where('is_overdue', true);
        if ($overdueTasks->isNotEmpty()) {
            $blockers[] = $overdueTasks->count() . ' task(s) are overdue and may need a date or scope check.';
        }

        $withoutDueDates = $context->whereNull('due_date');
        if ($withoutDueDates->count() > 0) {
            $blockers[] = $withoutDueDates->count() . ' task(s) do not have a due date.';
        }

        return array_slice($blockers, 0, 4);
    }

    protected function taskUrgency(array $task): string
    {
        if ($task['is_overdue'] || $task['priority'] === 'high' || ($task['days_until_due'] !== null && $task['days_until_due'] <= 2)) {
            return 'high';
        }

        if ($task['priority'] === 'medium' || $task['status'] === 'in_progress' || ($task['days_until_due'] !== null && $task['days_until_due'] <= 7)) {
            return 'medium';
        }

        return 'low';
    }

    protected function taskPriorityScore(array $task): int
    {
        $priorityScore = ['high' => 0, 'medium' => 300, 'low' => 600][$task['priority']] ?? 900;
        $dueScore = $task['days_until_due'] === null ? 250 : max(-100, (int) $task['days_until_due']);
        $statusScore = $task['status'] === 'in_progress' ? -50 : 0;
        $blockedPenalty = $task['is_blocked'] ? 1000 : 0;
        $overdueBoost = $task['is_overdue'] ? -500 : 0;

        return $blockedPenalty + $priorityScore + $dueScore + $statusScore + $overdueBoost;
    }

    protected function normalizeUrgency(mixed $urgency, array $task): string
    {
        $urgency = strtolower((string) $urgency);

        return in_array($urgency, ['high', 'medium', 'low'], true)
            ? $urgency
            : $this->taskUrgency($task);
    }

    protected function taskReason(array $task): string
    {
        if ($task['is_blocked']) {
            return 'This task is assigned to you but has an unfinished dependency.';
        }

        if ($task['is_overdue']) {
            return 'It is overdue and still open.';
        }

        if ($task['days_until_due'] !== null && $task['days_until_due'] <= 2) {
            return 'It is due very soon.';
        }

        if ($task['priority'] === 'high') {
            return 'It is marked as high priority.';
        }

        if ($task['status'] === 'in_progress') {
            return 'It is already in progress, so continuing it reduces context switching.';
        }

        return 'It is open and assigned to you.';
    }

    protected function taskAction(array $task): string
    {
        if ($task['is_blocked']) {
            return 'Check the dependency and coordinate with the project owner before starting.';
        }

        if ($task['status'] === 'in_progress') {
            return 'Continue the next deliverable and leave a progress update when the checkpoint is complete.';
        }

        if ($task['is_overdue']) {
            return 'Confirm the smallest shippable step, move it forward, and flag any delivery risk.';
        }

        return 'Review the task details, confirm the expected outcome, and move it into progress.';
    }

    protected function cleanText(mixed $value, int $limit): string
    {
        $value = trim(preg_replace('/\s+/', ' ', (string) $value));

        return Str::limit($value, $limit, '...');
    }
}
