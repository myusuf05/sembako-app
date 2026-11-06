<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('products')]
#[Fillable([
    'category_id',
    'barcode',
    'name',
    'unit',
    'purchase_price',
    'sale_price',
    'stock',
    'min_stock'
])]
class product extends Model
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(categories::class);
    }
}
