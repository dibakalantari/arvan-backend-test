<?php

namespace App\Services;

use App\User;

class UserService
{
    public function increaseUserBalance(User $user,float $amount)
    {
        $user->increment('balance',$amount);
    }

    public function decreaseUserBalance(User $user,float $amount)
    {
        $user->decrement('balance',$amount);
    }
}