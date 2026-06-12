<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('task'));
    }

    public function rules(): array
    {
        return [
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status'      => ['required', 'in:todo,in_progress,done'],
            'priority'    => ['required', 'in:low,medium,high'],
            'due_date'    => ['nullable', 'date'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'spent_hours' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
        ];
    }
}
