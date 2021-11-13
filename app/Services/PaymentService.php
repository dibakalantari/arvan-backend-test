<?php

namespace App\Services;

use App\Comment;
use App\Transaction;
use App\User;

class PaymentService
{
    public function create(User $user,array $data)
    {
       /** @var Transaction $transaction */
        $transaction = (new TransactionService())->createAndReturnTransaction($user->id, $data['amount']);

        (new FactorService())->createFactor($transaction, [
            'purchasable_id' => $data['purchasable_id'],
            'purchasable_type' => $data['purchasable_type']
        ]);

        (new UserService())->decreaseUserBalance($user->id, $data['amount']);
    }
}