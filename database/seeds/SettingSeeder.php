<?php

use App\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::truncate();

        Setting::insert([
            [
                'name' => Setting::REGISTERED_USER_CHARGE,
                'value' => 100000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => Setting::ARTICLE_FEE,
                'value' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => Setting::FREE_COMMENTS_COUNT,
                'value' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => Setting::COMMENT_FEE,
                'value' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => Setting::RECHARGE_NEEDED_CREDIT,
                'value' => 20000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => Setting::WAITING_HOURS_BEFORE_DELETING_USER,
                'value' => 24,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
