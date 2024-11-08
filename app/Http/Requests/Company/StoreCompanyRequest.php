<?php

namespace App\Http\Requests\Company;

use App\Rules\CocNumberCheck;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\UserRole;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(UserRole::SUPER_ADMIN->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:companies,name',
            'name_first' => 'required|string|max:255',
            'name_last' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'confirmPassword' => 'required|same:password',
            'phone' => 'required|string|max:255',
        ];
    }
}
