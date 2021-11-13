<?php

namespace App\Providers;

use App\Interfaces\NotificationChannel;
use App\Services\ArticleService;
use App\Services\AuthService;
use App\Services\CommentService;
use App\Services\FactorService;
use App\Services\MailService;
use App\Services\TagService;
use App\Services\TransactionService;
use App\Services\UserService;
use App\Services\SettingService;
use Illuminate\Support\ServiceProvider;

class ServiceClassesServiceProvider extends ServiceProvider
{
    public $singletons = [
        SettingService::class => SettingService::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            NotificationChannel::class,
            MailService::class
        );
    }
}
