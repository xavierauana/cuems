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