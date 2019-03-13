<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarbonCopy extends Model
{
    protected $fillable = [
        'name',
        'email',
        'type'
    ];


    // Relation
    public function notification(): BelongsTo {
        return $this->belongsTo(Notification::class);
    }
}
