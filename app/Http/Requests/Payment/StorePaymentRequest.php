<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'card_number' => $this->payment_method === 'credit_card' ? 'required' : '',
            'name_on_card' => $this->payment_method === 'credit_card' ? 'required' : '',
            'card_exp_month' => $this->payment_method === 'credit_card' ? 'required' : '',
            'card_exp_year' => $this->payment_method === 'credit_card' ? 'required' : '',
            'card_cvc' => $this->payment_method === 'credit_card' ? 'required' : '',
        ];
    }
}
