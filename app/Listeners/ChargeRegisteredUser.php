<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\UserService;
use App\Services\SettingService;
use App\Setting;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ChargeRegisteredUser implements ShouldQueue
{
     /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        try {
            $registeredUserCharge = app(SettingService::class)->getSettingValue(Setting::REGISTERED_USER_CHARGE);

            app(UserService::class)->increaseUserBalance($event->user,$registeredUserCharge);
        } catch (\Exception $exception) {
            Log::error("Error on charging registered user with id {$event->user->id} with this error :".$exception->getMessage());
        }
    }
}
