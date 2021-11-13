<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    protected $loggedInUser;

    protected $user;

    protected $headers;

    public function setUp()
    {
        parent::setUp();

        Artisan::call('db:seed');
        $users = factory(\App\User::class)->times(2)->create();

        $this->loggedInUser = $users[0];

        $this->user = $users[1];

        $this->headers = [
            'Authorization' => "Bearer {$this->loggedInUser->token}"
        ];
    }
}
