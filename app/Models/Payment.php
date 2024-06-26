<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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

    function scopeFiltered(Builder $query, ?string $keyword): Builder
    {
        $paymentTable = (new Payment())->getTable();

        return $query->when($keyword, function ($query, $keyword) use ($paymentTable) {
            return $query->where(function ($query) use ($keyword, $paymentTable) {
                $query->where("$paymentTable.payment_method", 'like', "%$keyword%")
                    ->orWhere("$paymentTable.name_on_card", 'like', "%$keyword%")
                    ->orWhere("$paymentTable.email", 'like', "%$keyword%")
                    ->orWhere("$paymentTable.amount", 'like', "%$keyword%")
                    ->orWhere("$paymentTable.customer_id", 'like', "%$keyword%")
                    ->orWhereRaw("RIGHT($paymentTable.card_number, 4) like ?", ["%$keyword%"])
                    ->orWhere("$paymentTable.transaction_id", 'like', "%$keyword%")
                    ->orWhere("$paymentTable.created_at", 'like', "%$keyword%");
            });
        });
    }

    function scopeOrdered(Builder $query, string $orderBy = 'id', string $order = 'asc'): Builder
    {
        return $query->orderBy($orderBy, $order);
    }
}
