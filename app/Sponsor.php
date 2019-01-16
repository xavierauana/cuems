<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Sponsor extends Model
{
    protected $fillable = [
        'name'
    ];

    // Relation

    public function event(): Relation {
        return $this->belongsTo(Event::class);
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
