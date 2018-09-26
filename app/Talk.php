<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Talk extends Model
{
    protected $fillable = [
        'title',
        'order'
    ];

    const StoreRules    = [
        'title' => 'required',
    ];
    const ErrorMessages = [];

    // scope

    public function scopeSorted(Builder $query): Builder {
        $query->latest()->orderBy('order');
    }
}
