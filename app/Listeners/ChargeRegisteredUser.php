<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\SettingService;
use App\Services\TransactionService;
use App\Services\UserService;
use App\Setting;

class ChargeRegisteredUser
{
    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        $registeredUserCharge = (new SettingService())->getSettingValue(Setting::REGISTERED_USER_CHARGE);

        (new UserService())->increaseUserCredit($event->user->id, $registeredUserCharge);

        (new TransactionService())->createAndReturnTransaction($event->user->id, $registeredUserCharge, true);
    }
}
