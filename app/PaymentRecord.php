<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @method static findOrFail($refId)
 */
class PaymentRecord extends Model
{
    use LogsActivity;

    protected $fillable = [
        'status',
        'invoice_id',
        'form_data',
        'event_id',
    ];

    protected static $logAttributes = [
        'status',
    ];

    protected static $logOnlyDirty = true;

    // Relation
    public function event(): Relation {
        return $this->belongsTo(Event::class);
    }

    public function setFormDataAttribute($value) {
        $this->attributes['form_data'] = encrypt($value);
    }

    public function getFormDataAttribute($value) {
        return decrypt($value);
    }

    // Scope
    public function scopeFailed(Builder $q): Builder {
        return $q->whereStatus('failed');
    }

    public function conversion(): Relation {
        return $this->hasOne(PaymentRecordConversion::class);
    }

    // Accessor
    public function getEmailAttribute(): ?string {
        return json_decode($this->form_data, true)['email'] ?? null;
    }
}
