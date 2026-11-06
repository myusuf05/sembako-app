<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('transactions')]
#[Fillable([
    'invoice_number',
    'user_id',
    'customer_id',
    'payment_method_id',
    'total_amount',
    'paid_amount',
    'change_amount',
    'status'
])]

class transactions extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(user::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(customers::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(payment_methods::class, 'payment_method_id');
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(transaction_details::class, 'transaction_id');
    }
}
