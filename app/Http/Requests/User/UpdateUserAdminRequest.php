<?php

namespace App\Http\Requests\User;

use App\Contracts\UserInfoRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserInfo;
use App\Rules\CocNumberCheck;
use Illuminate\Validation\Rule;

class UpdateUserAdminRequest extends FormRequest
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UserInfoRepositoryInterface $userInfoRepository
    ) {}
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
        // note that we do not want the user making the request,
        // but instead we want the uuid of the request URI.
        $user = $this->userRepository->getByUuidOrFail($this->uuid);

        return [
            'name_first' => 'required|string|max:255',
            'name_last' => 'required|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->email, 'email')],
            'phone' => 'required|string|max:255',
            'password' => 'sometimes|string|min:8',
            'confirmPassword' => 'sometimes|same:password',
            'role' => 'required|string|exists:roles,name',
            'avatar' => 'sometimes|nullable|image|mimes:jpg,jpeg,gif,png|max:1024',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'zip' => 'sometimes|string|max:255',
            'company' => ['sometimes', 'string', 'max:255', Rule::unique('companies', 'name')->ignore($user->company->id ?? null, 'id')],
        ];
    }
}
