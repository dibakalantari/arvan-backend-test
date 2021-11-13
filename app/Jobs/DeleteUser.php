<?php

namespace App\Jobs;

use App\Services\UserService;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->user->credit >= 0)
        {
            return;
        }

        DB::beginTransaction();
        try {
            (new UserService())->deleteUser($this->user);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error("Error on deleting user with id {$this->user->id} with this error :".$exception->getMessage());
        }
    }
}
