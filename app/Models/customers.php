<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;


#[Table('customers')]
#[Fillable(['name', 'phone', 'address'])]
class customers extends Model
{
    public function transactions()
    {
        return $this->hasMany(transactions::class, 'customer_id');
    }
}
