<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class ExpenseMedium extends Model
{
    protected $fillable = [
        'path',
    ];

    // Relation

    public function expense(): Relation {
        return $this->belongsTo(Expense::class);
    }

}
