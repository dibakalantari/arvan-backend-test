<?php

namespace App\Services;

use App\Interfaces\NotificationChannel;
use App\Jobs\DeleteUser;
use App\Setting;
use App\User;

class UserService
{
    public function increaseUserCredit(int $user_id,int $amount): void
    {
        /** @var User $user */
        $user = User::query()->lockForUpdate()->find($user_id);

        $user->increment('credit',$amount);

        $this->checkIfUserIsActivated($user);
    }

    public function decreaseUserBalance(int $user_id,int $amount): void
    {
        /** @var User $user */
        $user = User::query()->lockForUpdate()->find($user_id);

        $user->decrement('credit',$amount);

        $this->checkUserCreditAndSendNotification($user);

        $this->checkIfUserShouldBeInactive($user);
    }

    public function checkUserCreditAndSendNotification(User $user): void
    {
        $rechargeNeededCredit = (new SettingService())->getSettingValue(Setting::RECHARGE_NEEDED_CREDIT);

        if($user->credit <= (int)$rechargeNeededCredit && $user->isActive())
        {
            app(NotificationChannel::class)->send($user);

            $this->changeUserStatus($user,User::RECHARGE_NEEDED_STATUS);
        }
    }

    public function checkIfUserIsActivated(User $user): void
    {
        $rechargeNeededCredit = (new SettingService())->getSettingValue(Setting::RECHARGE_NEEDED_CREDIT);

        if($user->isRechargeNeeded() && $user->credit > $rechargeNeededCredit) {
            $this->changeUserStatus($user,User::ACTIVE_STATUS);
        }

        if($user->isInactive() && $user->credit >= 0) {
            $this->changeUserStatus($user,User::ACTIVE_STATUS);
        }
    }

    public function checkIfUserShouldBeInactive(User $user): void
    {
        if($user->credit < 0 && !$user->isInactive()) {
            $this->changeUserStatus($user,User::INACTIVE_STATUS);

            $waitingHoursBeforeDeletingUser = (new SettingService())->getSettingValue(Setting::WAITING_HOURS_BEFORE_DELETING_USER);

            DeleteUser::dispatch($user)->delay(now()->addHours($waitingHoursBeforeDeletingUser));
        }
    }

    public function changeUserStatus(User $user,string $status): void
    {
        $user->update([
           'status' => $status
        ]);
    }

    public function deleteUser(User $user)
    {
        $user->delete();
    }
}