<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class DelegateRole extends Model
{
    // Relation
    public function delegates(): Relation {
        return $this->belongsToMany(DelegateRole::class);
    }

}
