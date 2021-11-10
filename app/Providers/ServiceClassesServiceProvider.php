<?php

namespace App\Providers;

use App\Services\UserService;
use App\Services\SettingService;
use Illuminate\Support\ServiceProvider;

class ServiceClassesServiceProvider extends ServiceProvider
{
    public $singletons = [
        UserService::class => UserService::class,
        SettingService::class => SettingService::class,
    ];
}
