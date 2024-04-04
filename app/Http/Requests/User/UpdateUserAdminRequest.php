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
        return $this->user()->hasRole('admin');
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
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->email, 'email')],
            'name_first' => 'sometimes|required|string|max:255',
            'name_last' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->email, 'email')],
            'company' => ['sometimes', 'string', 'max:255', Rule::unique('user_infos', 'company')->ignore($user->info->id)],
            'coc_number' => ['sometimes', 'string'],
//            'coc_number' => ['required','string','numeric'
//            ,'unique:user_infos,coc_number,'. $info->id,
//            new CocNumberCheck()],
            'phone' => 'sometimes|string|unique:user_infos,phone,'. $user->info->id,
            'communication_language' => 'sometimes|string|in:nl,en',
            'oss_registered_at' => 'sometimes|date',
            'state' => 'sometimes|in:active,inactive',
        ];
    }
}
