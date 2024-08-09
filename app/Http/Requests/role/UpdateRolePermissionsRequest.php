<?php

namespace App\Http\Requests\role;

use App\Rules\CocNumberCheck;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRolePermissionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('Super Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'permissions' => 'required|array',
            'permissions.*.name' => 'required|string',
            'permissions.*.create' => 'boolean',
            'permissions.*.view' => 'boolean',
            'permissions.*.edit' => 'boolean',
            'permissions.*.delete' => 'boolean',
        ];
    }
}
