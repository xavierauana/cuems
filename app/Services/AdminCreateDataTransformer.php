<?php
/**
 * Author: Xavier Au
 * Date: 2019-03-05
 * Time: 10:05
 */

namespace App\Services;


class AdminCreateDataTransformer
{

    public static function transformRules(array $rules): array {
        $rules['institution'] = "nullable";
        $rules['address_1'] = "nullable";
        $rules['address_2'] = "nullable";
        $rules['address_3'] = "nullable";

        return $rules;
    }

    public static function transformInputs(array $data): array {
        $data['institution'] = (!isset($data['institution']) or $data['institution'] == "0") ? "empty" : $data['institution'];
        $data['position'] = (!isset($data['position']) or $data['position'] == "0") ? "empty" : $data['position'];
        $data['address_1'] = $data['address_1'] ?? "empty";
        $data['address_2'] = $data['address_2'] ?? "empty";
        $data['address_3'] = $data['address_3'] ?? "empty";
        return $data;
    }

}