<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value'
    ];
    protected $appends = [
        'urls',
    ];

    public function getUrlsAttribute() {
        return [
            'edit'   => route('events.settings.edit',
                [$this->event_id, $this->id]),
            'delete' => route('events.settings.destroy',
                [$this->event_id, $this->id]),
        ];
    }


    public function event(): Relation {
        return $this->belongsTo(Event::class);
    }
}
