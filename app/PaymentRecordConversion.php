<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class PaymentRecordConversion extends Model
{
    protected $fillable = [
        'status'
    ];

    // Relation
    public function record(): Relation {
        return $this->belongsTo(PaymentRecord::class);
    }
}
