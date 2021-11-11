<?php

namespace App\Services;

use App\Interfaces\NotificationChannel;
use App\Setting;
use App\User;

class UserService
{
    public function increaseUserBalance(User $user,float $amount)
    {
        $user->increment('credit',$amount);

        $this->checkIfUserIsActivated($user);
    }

    public function decreaseUserBalance(User $user,float $amount)
    {
        $user->decrement('credit',$amount);

        $this->checkUserCreditAndSendNotification($user);
    }

    public function checkUserCreditAndSendNotification(User $user) //TODO: you can transfer this to another AccountService
    {
        $rechargeNeededCredit = app(SettingService::class)->getSettingValue(Setting::RECHARGE_NEEDED_CREDIT);

        if($user->credit <= $rechargeNeededCredit && $user->isActive())
        {
            app(NotificationChannel::class)->send($user);

            $this->changeUserStatus($user,User::RECHARGE_NEEDED_STATUS);
        }
    }

    public function checkIfUserIsActivated(User $user)
    {
        if($user->isRechargeNeeded() && $user->credit > app(SettingService::class)->getSettingValue(Setting::RECHARGE_NEEDED_CREDIT)) {
            $this->changeUserStatus($user,User::ACTIVE_STATUS);
        }
    }

    public function changeUserStatus(User $user,string $status)
    {
        $user->update([
           'status' => $status
        ]);
    }
}