<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Factor extends Model
{
    protected $fillable = [
        'price',
        'transaction_id',
        'publishable_id',
        'publishable_type',
    ];
}
