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

        $data['institution'] = (isset($data['institution']) and $data['institution'] != 0) ? $data['institution'] : "empty";
        $data['position'] = (isset($data['position']) and $data['position'] != 0) ? $data['position'] : "empty";
        $data['address_1'] = $data['address_2'] ?? "empty";
        $data['address_2'] = $data['address_3'] ?? "empty";
        $data['address_3'] = $data['address_3'] ?? "empty";

        return $data;
    }

}