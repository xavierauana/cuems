<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Session extends Model
{

    protected $fillable = [
        'title',
        'subtitle',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at',
        'end_at',
    ];

    const StoreRules    = [
        'title'    => 'required',
        'subtitle' => 'required',
        'start_at' => 'required|date',
        'end_at'   => 'required|date|date_gt:start_at',
    ];
    const ErrorMessages = [
        'date_gt' => "End Date should later than stat date",
    ];

    // Relation
    public function event(): Relation {
        return $this->belongsTo(Event::class);
    }

    public function talks(): Relation {
        return $this->hasMany(Talk::class);
    }

    // Access
    public function getDurationAttribute(): string {
        return (new Carbon($this->start_at))->format("d M Y H:i") . " - " . (new Carbon($this->end_at))->format("H:i");
    }

    // Mutator

    public function setStartAtAttribute($value): void {
        $carbonInstance = Carbon::createFromFormat("d M Y H:i", $value);
        $this->attributes['start_at'] = $carbonInstance;
    }

    public function setEndAtAttribute($value): void {
        $carbonInstance = Carbon::createFromFormat("d M Y H:i", $value);
        $this->attributes['end_at'] = $carbonInstance;
    }


}
