<?php

namespace App\Http\Requests\project;

use Illuminate\Foundation\Http\FormRequest;

class StoreMileStoneRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'project_list_ids' => 'sometimes|array|exists:project_lists,id',
        ];
    }
}
