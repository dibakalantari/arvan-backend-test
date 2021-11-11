<?php

namespace App\Services;

use App\Exceptions\NotEnoughCreditException;
use App\User;

class UserService
{
    public function increaseUserBalance(User $user,float $amount)
    {
        $user->increment('credit',$amount);
    }

    public function decreaseUserBalance(User $user,float $amount)
    {
        if($user->credit < $amount)
        {
            throw new NotEnoughCreditException(); //TODO: write test for this error message
        }

        $user->decrement('credit',$amount);
    }
}