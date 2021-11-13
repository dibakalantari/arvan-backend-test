<?php

namespace Tests\Unit;

use App\Events\UserRegistered;
use App\Services\SettingService;
use App\Setting;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserRegisteredEventTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function firing_user_registered_event_increases_user_credit()
    {
        $user = factory(User::class)->create();

        event(new UserRegistered($user));

        $registeredUserCharge = (new SettingService())->getSettingValue(Setting::REGISTERED_USER_CHARGE);

        $this->assertEquals($user->fresh()->credit,$registeredUserCharge);
    }
}
