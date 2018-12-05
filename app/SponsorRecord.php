<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class SponsorRecord extends Model
{
    //

    protected $fillable = [
        'sponsor_id',
        'tel',
        'name',
        'email',
        'address',
    ];

    public function delegate(): Relation {
        return $this->belongsTo(Delegate::class);
    }

    public function sponsor(): Relation {
        return $this->belongsTo(Sponsor::class);
    }
}
