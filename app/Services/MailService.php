<?php

namespace App\Services;

use App\Interfaces\NotificationChannel;
use App\Notifications\RechargeNeeded;
use App\User;
use Illuminate\Support\Facades\Notification;

class MailService implements NotificationChannel
{
    public function send(User $user)
    {
        Notification::send($user, new RechargeNeeded('mail'));
    }
}