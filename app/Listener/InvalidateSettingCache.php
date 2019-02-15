<?php
/**
 * Author: Xavier Au
 * Date: 2019-01-08
 * Time: 14:31
 */

namespace App\Listener;


use App\Setting;

class InvalidateSettingCache
{
    /**
     * @var \App\Setting
     */
    private $setting;

    /**
     * InvalidateSettingCache constructor.
     * @param \App\Setting $setting
     */
    public function __construct(Setting $setting) {
        $setting->invalidateCache();
    }

    public function handle() {
    dd("handling");
    }
}