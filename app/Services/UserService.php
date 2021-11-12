<?php

namespace App\Services;

use App\Interfaces\NotificationChannel;
use App\Jobs\DeleteUser;
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

        $this->checkIfUserShouldBeInactive($user);
    }

    public function checkUserCreditAndSendNotification(User $user)
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

        if($user->isInactive() && $user->credit >= 0) {
            $this->changeUserStatus($user,User::ACTIVE_STATUS);
        }
    }

    public function checkIfUserShouldBeInactive(User $user)
    {
        if($user->credit < 0 && !$user->isInactive()) {
            $this->changeUserStatus($user,User::INACTIVE_STATUS);

            DeleteUser::dispatch($user)->delay(now()->addDay());
        }
    }

    public function changeUserStatus(User $user,string $status)
    {
        $user->update([
           'status' => $status
        ]);
    }
}