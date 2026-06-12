<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('project'));
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status'      => ['required', 'in:pending,in_progress,completed'],
            'priority'    => ['required', 'in:low,medium,high'],
            'due_date'    => ['nullable', 'date'],
            'members'     => ['nullable', 'array'],
            'members.*'   => ['exists:users,id'],
        ];
    }
}
