<?php

namespace App\Services;

use App\Transaction;
use App\User;

class TransactionService
{
    public function createAndReturnTransaction(int $user_id,int $amount,$increment = false)
    {
        return Transaction::query()->create([
            'amount' => $amount,
            'user_id' => $user_id,
            'increment' => $increment,
        ]);
    }
}