<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PositionGrouping extends Model
{
    protected $fillable = [
        'position',
        'grouping'
    ];
}
