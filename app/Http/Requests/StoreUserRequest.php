<?php

namespace App\Http\Requests;

use App\Rules\CocNumberCheck;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\UserRole;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(UserRole::ADMIN->value) || $this->user()->hasRole(UserRole::SUPER_ADMIN->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name_first' => 'required|string|max:255',
            'name_last' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'confirmPassword' => 'required|same:password',
            'phone' => 'required|string|max:255',
            'role' => 'required|string|exists:roles,name',
            'avatar' => 'sometimes|image|mimes:jpg,jpeg,gif,png|max:1024',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'american_state' => 'sometimes|string|max:255',
            'zip' => 'sometimes|string|max:255',
            'company_name' => ['sometimes', 'string', 'max:255'],
            'company_logo' => 'sometimes|nullable|image|mimes:jpg,jpeg,gif,png|max:2048',
        ];
    }
}
