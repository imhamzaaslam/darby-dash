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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_type_id' => 'required|exists:project_types,id',
            'title' => 'required|string|max:255',
            'description' => 'sometimes|string',
            'member_ids' => [
                'sometimes',
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    $members = User::whereIn('id', $value)->get();
                    if (count($value) !== $members->count()) {
                        $fail('One or more member ids are invalid.');
                    }
                },
            ],
            'est_hours' => 'sometimes|nullable|integer',
            'est_budget' => 'sometimes|nullable|integer',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'status' => 'sometimes|in:active,inactive',
            'budget_amount' => 'sometimes|nullable',
            'bucks_share' => 'sometimes|nullable',
            'bucks_share_type' => 'sometimes|nullable|in:fixed,percentage',
        ];
    }
}
