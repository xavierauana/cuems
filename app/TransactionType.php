<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class TransactionType extends Model
{
    public function transactions(): Relation {
        return $this->hasMany(Transaction::class);
    }
}
