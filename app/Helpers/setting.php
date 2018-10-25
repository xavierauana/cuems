<?php
/**
 * Author: Xavier Au
 * Date: 24/10/2018
 * Time: 6:16 AM
 */

use App\Setting;

if (!function_exists('setting')) {
    function setting(string $key): ?string {
        return optional(Setting::where("key", $key)->first())->value;
    }
}