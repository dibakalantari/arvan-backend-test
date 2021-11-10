<?php

namespace App\Providers;

use App\Services\FactorService;
use App\Services\TransactionService;
use App\Services\UserService;
use App\Services\SettingService;
use Illuminate\Support\ServiceProvider;

class ServiceClassesServiceProvider extends ServiceProvider
{
    public $singletons = [
        UserService::class => UserService::class,
        SettingService::class => SettingService::class,
        TransactionService::class => TransactionService::class,
        FactorService::class => FactorService::class,
    ];
}
