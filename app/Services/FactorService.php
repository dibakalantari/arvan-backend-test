<?php

namespace App\Services;

use App\Factor;
use App\Transaction;

class FactorService
{
    public function createFactor(Transaction $transaction,array $data): void
    {
        $transaction->factor()->create([
            'purchasable_id' => $data['purchasable_id'],
            'purchasable_type' => $data['purchasable_type'],
        ]);
    }
}