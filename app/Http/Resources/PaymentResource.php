<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'amount' => strpos($this->amount, '.') ? number_format((float)$this->amount, 2, '.', '')  : $this->amount,
            'payment_method' => $this->payment_method,
            'card_number' => $this->card_number ? 'XXXX' . substr($this->card_number, -4) : null,
            'name_on_card' => $this->name_on_card,
            'card_exp_month' => $this->card_exp_month,
            'card_exp_year' => $this->card_exp_year,
            'card_cvc' => $this->card_cvc,
            'created_at' => format_date($this->created_at),
        ];
    }
}
