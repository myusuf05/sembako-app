<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('payment_methods')]
#[Fillable(['name', 'is_active'])]
class payment_methods extends Model {}
