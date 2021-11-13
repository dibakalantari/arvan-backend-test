<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
      'amount',
      'user_id',
      'increment',
    ];

    protected $casts = [
      'increment' => 'boolean'
    ];

    public function factor()
    {
        return $this->hasOne(Factor::class);
    }
}
