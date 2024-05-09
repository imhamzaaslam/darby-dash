<?php

namespace App\Http\Requests\project;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreProjectRequest extends FormRequest
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
            'project_type_id' => 'sometimes|exists:project_types,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'project_manager_id' => 'sometimes|exists:users,id',
            'member_ids' => 'sometimes|string',
            'member_ids' => [
                'sometimes',
                'string',
                function ($attribute, $value, $fail) {
                    $member_ids = explode(',', $value);
                    $members = User::whereIn('id', $member_ids)->get();
                    if (count($member_ids) !== $members->count()) {
                        $fail('One or more member ids are invalid.');
                    }
                },
            ],
            'est_hours' => 'sometimes|integer',
            'est_budget' => 'sometimes|integer',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'status' => 'sometimes|in:active,inactive',
        ];
    }
}
