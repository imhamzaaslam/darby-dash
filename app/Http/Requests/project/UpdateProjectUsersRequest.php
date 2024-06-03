<?php

namespace App\Http\Requests\project;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class UpdateProjectUsersRequest extends FormRequest
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
            'member_ids' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $members = User::whereIn('id', $value)->get();
                    if (count($value) !== $members->count()) {
                        $fail('One or more member ids are invalid.');
                    }
                },
            ],
        ];
    }
}
