<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Delegate extends Model
{
    // Relation
    public function event(): Relation {
        return $this->belongsTo(Event::class);
    }

    public function roles(): Relation {
        return $this->belongsToMany(DelegateRole::class);
    }

    // Accessor
    public function getNameAttribute(): string {
        return $this->surname . " " . $this->given_name;
    }
}
