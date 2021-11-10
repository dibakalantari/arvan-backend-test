<?php

namespace App\Services;

use App\Factor;

class FactorService
{
    public function createFactor(int $transaction_id,int $purchasable_id,string $purchasable_type)
    {
        Factor::query()->create([
            'transaction_id' => $transaction_id,
            'purchasable_id' => $purchasable_id,
            'purchasable_type' => $purchasable_type,
        ]);
    }
}