<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Setting extends Model
{
    public function event(): Relation {
        return $this->belongsTo(Event::class);
    }
}
