<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Template extends Model
{
    protected $appends = [
        "urls"
    ];

    // Relation

    public function event(): Relation {
        return $this->belongsTo(Event::class);
    }

    public function getUrlsAttribute(): array {
        return [
            'edit'   => route('events.templates.edit', $this->event_id),
            'delete' => route('events.templates.destroy', $this->event_id),
        ];
    }
}
