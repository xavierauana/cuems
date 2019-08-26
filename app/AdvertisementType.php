<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdvertisementType extends Model
{
    protected $fillable = [
        'name'
    ];

    public static function StoreRules(): array {
        return [
            'name' => 'required'
        ];
    }

    // Relation
    public function advertisements(): HasMany {
        return $this->hasMany(Advertisement::class);
    }
}
