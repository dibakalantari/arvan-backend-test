<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Factor extends Model
{
    protected $fillable = [
        'transaction_id',
        'purchasable_id',
        'purchasable_type',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
