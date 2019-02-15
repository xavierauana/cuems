<?php
/**
 * Author: Xavier Au
 * Date: 25/10/2018
 * Time: 6:28 PM
 */

namespace App\Traits;


use Carbon\Carbon;

trait FlatpickrConversion
{
    private function convert(string $value): string {
        $carbon = new Carbon($value);

        return $carbon->format("d M Y H:i");
    }
}