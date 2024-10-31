<?php

namespace App\Http\Requests\chat;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreChatMessageRequest extends FormRequest
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
            'message' => 'sometimes|nullable|string|max:255',
            'chatId' => 'sometimes|nullable:chat_messages,id',
            'files.*' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ];
    }
}
