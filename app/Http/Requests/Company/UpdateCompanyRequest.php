<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CocNumberCheck;
use Illuminate\Validation\Rule;
use App\Models\User;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $userUuid = $this->input('user_uuid');
        $user= User::where('uuid', $userUuid)->first();
        $company = $user ? $user->company : null;
        return [
            'user_uuid' => 'sometimes|required|string',
            'name' => 'sometimes|required|string|max:255|unique:companies,name,'.$company->id,
            'name_first' => 'sometimes|required|string|max:255',
            'name_last' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,'.$user->id,
            'phone' => 'sometimes|required|string|max:255',
        ];
    }
}
