<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class Sponsor extends Model
{
    protected $fillable = [
        'name'
    ];

    // Relation

    public function event(): Relation {
        return $this->belongsTo(Event::class);
    }

    public function records(): Relation {
        return $this->hasMany(SponsorRecord::class);
    }

    // Accessor

    public function getDelegatesAttribute(): Collection {
        return $this->records->map->delegate;
    }

    // Helpers

    public function getStoreRules($param): array {
        return [
            'name' => 'required'
        ];
    }

    public function getUpdateRules($param): array {
        return $this->getStoreRules($param);
    }

    public function getValidationMessage(): array {
        return [];
    }
}
