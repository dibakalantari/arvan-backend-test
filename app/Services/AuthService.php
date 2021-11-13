<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\User;

class AuthService
{
    public function register(array $data): User
    {
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        event(new UserRegistered($user));

        return $user;
    }
}