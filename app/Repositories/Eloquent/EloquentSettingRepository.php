<?php

namespace App\Repositories\Eloquent;

use App\Contracts\SettingRepositoryInterface;
use App\Models\Setting;

class EloquentSettingRepository implements SettingRepositoryInterface
{
    public function get(string $key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public function set(string $key, $value): Setting
    {
        return Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
