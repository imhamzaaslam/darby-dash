<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\UserRole;

class StoreCompanyThemeColorsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(UserRole::SUPER_ADMIN->value) || $this->user()->hasRole(UserRole::ADMIN->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'primary_color' => 'required|regex:/^#([A-Fa-f0-9]{3}){1,2}$/',
        ];
    }
}
