<?php

namespace App\Http\Requests\task;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class AssignTaskRequest extends FormRequest
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
            'assignee' => [
                'required',
                'exists:project_members,user_id',
                function ($attribute, $value, $fail) {
                    if (User::find($value)?->roles->contains('id', 2)) {
                        $fail('Cannot assign a user with this role.');
                    }
                },
            ],
        ];
    }
}
