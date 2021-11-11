<?php

namespace App\Interfaces;

use App\User;

interface NotificationChannel
{
    public function send(User $user);
}