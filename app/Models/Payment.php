<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Base;

class Payment extends Base
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_method',
        'name_on_card',
        'card_number',
        'card_exp_month',
        'card_exp_year',
        'card_cvc',
        'email',
        'amount',
        'customer_id',
        'transaction_id',
        'display_order',
    ];
}
