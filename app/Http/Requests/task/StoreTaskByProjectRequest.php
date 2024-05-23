<?php

namespace App\Http\Requests\task;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreTaskByProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'board_id' => 'sometimes|exists:project_boards,id',
            'list_id' => 'sometimes|nullable|exists:project_lists,id',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|nullable|in:todo,in_progress,completed',
            'start_date' => 'sometimes|nullable|date',
            'due_date' => 'sometimes|nullable|date',
            'time_spent' => 'sometimes|nullable|integer',
        ];
    }
}
