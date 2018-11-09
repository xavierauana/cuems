<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Vendor extends Model
{

    protected $fillable = [
        'name'
    ];

    // Relation
    public function expenses(): Relation {
        return $this->hasMany(Expense::class);
    }
}
