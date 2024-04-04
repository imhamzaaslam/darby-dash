<?php

namespace App\Http\Requests;

use App\Rules\CocNumberCheck;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
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
            'company' => 'required_if:role,customer|string|max:255',
//            'coc_number' => ['required','string','numeric'
//            ,'unique:user_infos,coc_number',
//            new CocNumberCheck()],
            'coc_number' => 'required_if:role,customer|string|numeric',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,customer',
        ];
    }
}
