<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Transaction extends Model
{
    protected $fillable = [
        'charge_id',
        'card_brand',
        'last_4',
        'ticket_id',
        'status',
    ];

    // Relation

    public function payee(): Relation {
        return $this->morphTo();
    }

    public function ticket(): Relation {
        return $this->belongsTo(Ticket::class);
    }
}
