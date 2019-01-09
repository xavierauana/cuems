<?php
/**
 * Author: Xavier Au
 * Date: 24/10/2018
 * Time: 6:16 AM
 */

use App\Event;

if (!function_exists('setting')) {
    function setting(Event $event, string $key): ?string {
        $cacheKey = "settings_{$event->id}_{$key}";

        if (cache()->has($cacheKey)) {
            \Debugbar::info('load from cache');
            return cache($cacheKey);
        } else {
            \Debugbar::info('load from db');
            $value = optional($event->settings()->where('key', $key)
                                    ->first())->value;
            $minutes = 10;
            cache()->put($cacheKey, $value, $minutes);

            return $value;
        }
    }
}