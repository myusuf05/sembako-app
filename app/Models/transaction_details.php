<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('transaction_details')]
#[Fillable([
    'transaction_id',
    'product_id',
    'quantity',
    'unit_price',
    'subtotal'
])]

class transaction_details extends Model
{
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(transactions::class, 'transaction_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(product::class, 'product_id');
    }
}
