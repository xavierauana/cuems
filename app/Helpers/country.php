<?php
/**
 * Author: Xavier Au
 * Date: 24/10/2018
 * Time: 6:16 AM
 */

if (!function_exists('getCountiesList')) {
    function getCountiesList(): array {
        $content = file_get_contents(storage_path("app/countries.json"));

        $array = json_decode($content);

        return array_combine($array, $array);
    }
}
if (!function_exists('getPositionList')) {
    function getPositionList() {

        if (!$positions = cache('positions')) {
            $positions = \App\Position::pluck('name');

            cache()->put('positions', $positions, 10);
        }

        return $positions;

    }
}

