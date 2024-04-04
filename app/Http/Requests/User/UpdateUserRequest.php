<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserInfo;
use App\Rules\CocNumberCheck;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $info = $this->user()->info;

        return [
            'name_first' => 'sometimes|required|string|max:255',
            'name_last' => 'sometimes|required|string|max:255',
            'company' => 'sometimes|string|max:255|unique:user_infos,company,' . $info->id,
            'coc_number' => ['sometimes','string','numeric','unique:user_infos,coc_number,'. $info->id, new CocNumberCheck()],
            'email' => 'sometimes|email|unique:users,email,'. $this->user()->id,
            'phone' => 'sometimes|string|unique:user_infos,phone,'. $info->id,
            'oss_registered_at' => 'nullable|sometimes|date',
            'bookkeeping_started_at' => 'nullable|sometimes|date',
            'communication_language' => 'sometimes|string|in:nl,en',
            'yuki_access_key' => ['nullable','sometimes', Rule::unique('users', 'yuki_access_key')->ignore($this->user()->id)],
        ];
    }
}
