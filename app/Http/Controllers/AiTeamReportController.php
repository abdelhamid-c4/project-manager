<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\AiProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiTeamReportController extends Controller
{
    public function __invoke(Request $request, AiProjectService $aiProjectService): JsonResponse
    {
        $user = $request->user();
        $report = $aiProjectService->generateTeamMemberReport($user);
        $taskIds = collect($report['tasks'] ?? [])->pluck('id')->filter()->all();

        $tasks = Task::query()
            ->where('assigned_to', $user->id)
            ->whereIn('id', $taskIds)
            ->get()
            ->keyBy('id');

        $report['tasks'] = collect($report['tasks'] ?? [])
            ->map(function (array $task) use ($tasks, $user) {
                $taskModel = $tasks->get($task['id'] ?? null);

                if (!$taskModel) {
                    return null;
                }

                $canUpdate = $user->can('update', $taskModel);

                return [
                    ...$task,
                    'url' => $canUpdate
                        ? route('tasks.edit', $taskModel)
                        : route('tasks.show', $taskModel),
                    'action_label' => $canUpdate ? 'Edit task' : 'View task',
                ];
            })
            ->filter()
            ->values()
            ->all();

        return response()->json([
            'report' => $report,
        ]);
    }
}
