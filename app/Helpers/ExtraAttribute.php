<?php
/**
 * Author: Xavier Au
 * Date: 2019-08-24
 * Time: 17:15
 */

namespace App\Helpers;


use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class ExtraAttribute extends SchemalessAttributes
{
    public static function scopeOrWithSchemalessAttributes(string $attributeName
    ): Builder {
        $arguments = debug_backtrace()[1]['args'];

        if (count($arguments) === 1) {
            [$builder] = $arguments;
            $schemalessAttributes = [];
        }

        if (count($arguments) === 2) {
            [$builder, $schemalessAttributes] = $arguments;
        }

        if (count($arguments) >= 3) {
            [$builder, $name, $value] = $arguments;
            $schemalessAttributes = [$name => $value];
        }

        foreach ($schemalessAttributes as $name => $value) {
            $builder->orWhere("{$attributeName}->{$name}", 'like',
                "%{$value}%");
        }

        return $builder;
    }

}