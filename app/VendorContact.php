<?php

namespace App;

use App\AbstractClasses\AbstractModelClass;

class VendorContact extends AbstractModelClass
{
    protected $fillable = [
        'name',
        'email',
        'tel',
        'fax',
    ];

    // Relation


    // Helpers

    /**
     * @param array $params
     * @return array
     */
    public function getStoreRules(array $params = []): array {
        return [
            'name'  => "required",
            'email' => "nullable|email",
            'tel'   => "nullable|",
            'fax'   => "nullable|",
        ];
    }
}
