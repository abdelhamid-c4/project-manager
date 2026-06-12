<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Project::class);
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status'      => ['required', 'in:pending,in_progress,completed'],
            'priority'    => ['required', 'in:low,medium,high'],
            'due_date'    => ['nullable', 'date', 'after_or_equal:today'],
            'members'     => ['nullable', 'array'],
            'members.*'   => ['exists:users,id'],
        ];
    }
}
