<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Ticket extends Model
{

    const StoreRules    = [
        'name'      => 'required',
        'price'     => 'required|min:0|numeric',
        'vacancy'   => 'nullable|numeric',
        'is_public' => 'nullable|boolean',
        'start_at'  => 'required|date',
        'end_at'    => 'required|date|date_gt:start_at',
        'note'      => 'nullable',
    ];
    const ErrorMessages = [];

    protected $fillable = [
        'name',
        'note',
        'price',
        'end_at',
        'vacancy',
        'start_at',
        'is_public',
    ];

    protected $casts = [
        'start_at' => "datetime",
        'end_at'   => "datetime",
    ];

    // Relation
    public function event(): Relation {

        return $this->belongsTo(Event::class);
    }

    // Scope
    public function scopeAvailable(Builder $query): void {
        $now = Carbon::now();
        $query->where([
            ['start_at', "<=", $now],
            ['end_at', ">=", $now],
        ]);
    }

    public function scopePublic(Builder $query): void {
        $query->whereIsPublic(true);
    }

    // Accessor
    public function getIsValidAttribute(): bool {

        $now = Carbon::now();

        return $now->between($this->start_at, $this->end_at);
    }

    public function getPriceAttribute($value): float {
        return floatval($value / 100);
    }

    public function getTemplateAttribute(): string {
        return 'ticket';
    }

    public function getTemplateDimensionAttribute(): array {
        return array(0, 0, 175, 500);
    }

    // Mutator
    public function setPriceAttribute($value): void {
        $this->attributes['price'] = $value * 100;
    }

    public function setStartAtAttribute($value): void {
        if ($value instanceof Carbon) {
            $this->attributes['start_at'] = $value;

        } else {
            $carbonInstance = Carbon::createFromFormat("d M Y H:i", $value);
            $this->attributes['start_at'] = $carbonInstance;
        }

    }

    public function setEndAtAttribute($value): void {
        if ($value instanceof Carbon) {
            $this->attributes['end_at'] = $value;
        } else {

            $carbonInstance = Carbon::createFromFormat("d M Y H:i", $value);
            $this->attributes['end_at'] = $carbonInstance;
        }

    }

    // Helpers
    public function hasSeat(): bool {
        if ($this->seat === null) {
            return true;
        }

        return Transaction::whereTicketId($this->id)->count() < $this->vacancy;
    }

}
