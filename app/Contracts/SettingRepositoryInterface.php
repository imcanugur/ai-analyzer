<?php

namespace App\Contracts;

use App\Models\Setting;

interface SettingRepositoryInterface
{
    /**
     * Get a setting value by key.
     */
    public function get(string $key, $default = null);

    /**
     * Set a setting value by key.
     */
    public function set(string $key, $value): Setting;
}
