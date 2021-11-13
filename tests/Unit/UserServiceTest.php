<?php

namespace Tests\Unit;

use App\Jobs\DeleteUser;
use App\Notifications\RechargeNeeded;
use App\Services\SettingService;
use App\Services\UserService;
use App\Setting;
use App\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    /** @test */
    public function when_user_credit_is_lower_or_equal_to_the_lowest_possible_credit_their_status_should_change_to_recharge_needed()
    {
        /** @var int $commentFee */
        $rechargeNeededCredit = app(SettingService::class)->getSettingValue(Setting::RECHARGE_NEEDED_CREDIT);

        $this->user->update([
            'credit' => $rechargeNeededCredit + 10000
        ]);

        Notification::fake();

        (new UserService())->decreaseUserBalance($this->user->id, 10000);

        $this->assertEquals(User::RECHARGE_NEEDED_STATUS, $this->user->fresh()->status);
    }

    /** @test */
    public function when_user_credit_is_lower_or_equal_to_the_lowest_possible_credit_user_should_be_notified()
    {
        /** @var int $commentFee */
        $rechargeNeededCredit = app(SettingService::class)->getSettingValue(Setting::RECHARGE_NEEDED_CREDIT);

        $this->user->update([
            'credit' => $rechargeNeededCredit + 10000
        ]);

        Notification::fake();

        (new UserService())->decreaseUserBalance($this->user->id, 10000);

        Notification::assertSentTo(
            $this->user,
            RechargeNeeded::class
        );
    }

    /** @test */
    public function users_shouldnt_get_notified_for_recharge_if_their_status_is_already_recharge_needed()
    {
        /** @var int $commentFee */
        $rechargeNeededCredit = app(SettingService::class)->getSettingValue(Setting::RECHARGE_NEEDED_CREDIT);

        $this->user->update([
            'credit' => $rechargeNeededCredit,
            'status' => User::RECHARGE_NEEDED_STATUS
        ]);

        Notification::fake();

        (new UserService())->decreaseUserBalance($this->user->id, 10000);

        Notification::assertNotSentTo(
            $this->user,
            RechargeNeeded::class
        );
    }

    /** @test */
    public function when_users_charge_their_account_their_status_should_change_from_recharge_needed_to_active()
    {
        /** @var int $commentFee */
        $rechargeNeededCredit = app(SettingService::class)->getSettingValue(Setting::RECHARGE_NEEDED_CREDIT);

        $this->user->update([
            'credit' => $rechargeNeededCredit,
            'status' => User::RECHARGE_NEEDED_STATUS
        ]);

        (new UserService())->increaseUserCredit($this->user->id,10000);

        $this->assertEquals(User::ACTIVE_STATUS, $this->user->fresh()->status);
    }

    /** @test */
    public function users_with_negative_credit_should_be_inactive_and_DeleteUser_job_should_be_pushed_to_queue_with_delay()
    {
        $this->user->update([
            'credit' => 10000,
            'status' => User::RECHARGE_NEEDED_STATUS,
        ]);

        Queue::fake();

        (new UserService())->decreaseUserBalance($this->user->id, 11000);

        Queue::assertPushed(DeleteUser::class, function ($job) {
            return ! is_null($job->delay);
        });

        $this->assertEquals(User::INACTIVE_STATUS, $this->user->fresh()->status);
    }
}
