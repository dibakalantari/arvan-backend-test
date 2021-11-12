<?php

namespace App\Services;

use App\Setting;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class SettingService
{
    public function getSettingValue(string $name)
    {
        $setting = Setting::query()->where('name',$name)->first();

        if(!$setting)
        {
            throw new NotFoundResourceException();
        }

        return $setting->value;
    }

    public function updateSettingValue(Setting $setting,$value)
    {
        $setting->update([
           'value' => $value
        ]);
    }
}