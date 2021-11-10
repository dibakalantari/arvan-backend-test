<?php

namespace App\Services;

use App\User;

class UserService
{
    public function increaseUserBalance(User $user,float $amount)
    {
        $user->increment('balance',$amount);
    }
}