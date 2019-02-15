<?php

namespace App;

use App\Traits\FlatpickrConversion;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;

/**
 * @method static findOrFail($ticket_id)
 */
class Ticket extends Model
{
    use FlatpickrConversion;

    const ErrorMessages = [];

    protected $fillable = [
        'code',
        'name',
        'note',
        'price',
        'end_at',
        'vacancy',
        'template',
        'start_at',
        'is_public',
    ];

    protected $casts = [
        'start_at'     => 'datetime',
        'end_at'       => 'datetime',
        'is_available' => 'boolean',
    ];

    protected $appends = [
        'is_available'
    ];

    // Validation Rules

    /**
     * @param null $event_id
     * @return array
     */
    public function getStoreRules($event_id = null): array {
        // TODO set code to be unique for each Event'

        $rules = [
            'name'      => 'required',
            'code'      => ['required'],
            'price'     => 'required|min:0|numeric',
            'vacancy'   => 'nullable|numeric',
            'is_public' => 'nullable|boolean',
            'start_at'  => 'required|date',
            'end_at'    => 'required|date|date_gt:start_at',
            'note'      => 'nullable',
            'template'  => 'nullable',
        ];

        if ($event_id) {
            $rules['code'][] = Rule::unique('tickets')->where(function ($query
            ) use (
                $event_id
            ) {
                return $query->where('event_id', $event_id);
            });
        }

        return $rules;
    }

    public function getUpdateRules($event_id = null): array {
        // TODO set code to be unique for each Event'

        $rules = [
            'name'      => 'required',
            'code'      => [
                'required',
                Rule::unique('tickets')->where(function ($query
                ) use (
                    $event_id
                ) {
                    return $query->where('event_id', $event_id);
                })->ignore($this->id),
            ],
            'price'     => 'required|min:0|numeric',
            'vacancy'   => 'nullable|numeric',
            'is_public' => 'nullable|boolean',
            'start_at'  => 'required|date',
            'end_at'    => 'required|date|date_gt:start_at',
            'note'      => 'nullable',
            'template'  => 'nullable',
        ];

        return $rules;
    }

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

    public function getStartAtAttribute($value): string {
        return $this->convert($value);
    }

    public function getEndAtAttribute($value): string {
        return $this->convert($value);
    }

    public function getStartAtObjectAttribute() {

        return new Carbon($this->attributes['start_at']);
    }

    public function getEndAtObjectAttribute() {
        return new Carbon($this->attributes['end_at']);
    }

    public function getIsAvailableAttribute() {

        $now = Carbon::now();
        $start_at = new Carbon($this->getAttributes()['start_at']);
        $end_at = new Carbon($this->getAttributes()['end_at']);

        return $now->greaterThanOrEqualTo($start_at) and $end_at->greaterThanOrEqualTo($now);
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
