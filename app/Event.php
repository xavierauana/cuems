<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'end_at',
        'start_at',
    ];

    protected $casts = [
        'end_at'   => 'date',
        'start_at' => 'date',
    ];

    const StoreRules = [
        'title'    => 'required',
        'start_at' => 'required|date',
        'end_at'   => 'required|date|date_gt:start_at',
    ];

    const ValidationMessages = [
        'date_gt' => 'The end date must greater than start date.',
    ];

    // Relation
    public function delegates(): Relation {
        return $this->hasMany(Delegate::class);
    }

    public function sessions(): Relation {
        return $this->hasMany(Session::class);
    }

    public function tickets(): Relation {
        return $this->hasMany(Ticket::class);
    }

}
