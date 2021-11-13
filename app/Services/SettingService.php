<?php

namespace App\Services;

use App\Setting;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class SettingService
{
    public function getAllSettings()
    {
        return Setting::all();
    }

    public function getSettingValue(string $name): string
    {
        $setting = Setting::query()->where('name',$name)->first();

        if(!$setting)
        {
            throw new NotFoundResourceException();
        }

        cache()->rememberForever(Setting::$cacheKey."_{$setting->name}",$setting->value());

        return $setting->value;
    }

    public function updateSettingValue(Setting $setting,$value): void
    {
        tap($setting)->update([
           'value' => $value
        ]);

        cache()->forget(Setting::$cacheKey."_{$setting->name}");
    }
}