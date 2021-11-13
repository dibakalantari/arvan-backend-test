<?php

namespace Tests\Unit;

use App\Article;
use App\Comment;
use App\Factor;
use App\Jobs\DeleteUser;
use App\Transaction;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteUserJobTest extends TestCase
{
    /** @test */
    public function delete_user_if_its_credit_is_negative()
    {
        $this->user->update([
            'credit' => -10000
        ]);

        dispatch_now(new DeleteUser($this->user));

        $this->assertDatabaseMissing((new User())->getTable(),[
           'id' => $this->user->id
        ]);
    }
}
