<?php

namespace App;

use App\Helpers\ExtraAttribute;
use App\Traits\FlatpickrConversion;
use Carbon\Carbon;
use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class Session extends Model
{
    use FlatpickrConversion, FormAccessible;

    protected $fillable = [
        'title',
        'order',
        'venue',
        'end_at',
        'sponsor',
        'start_at',
        'subtitle',
        'moderation_type',
    ];

    protected $casts = [
        'start_at'         => 'datetime',
        'end_at'           => 'datetime',
        'extra_attributes' => 'array',
    ];

    const StoreRules    = [
        'title'            => 'required',
        'venue'            => 'nullable',
        'subtitle'         => 'nullable',
        'sponsor'          => 'nullable',
        'moderation_type'  => 'nullable|numeric',
        'moderators'       => 'nullable',
        'start_at'         => 'required|date',
        'end_at'           => 'required|date|date_gt:start_at',
        'order'            => 'nullable|numeric|min:0',
        'extra_attributes' => 'nullable'
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

    public function moderators(): Relation {
        return $this->belongsToMany(Delegate::class, 'moderators')
                    ->withPivot(['order', 'created_at'])
                    ->orderBy('pivot_order');
    }

    // Access
    public function getDurationAttribute(): string {
        return (new Carbon($this->start_at))->format("d M Y H:i") . " - " . (new Carbon($this->end_at))->format("H:i");
    }

    public function getModeratorDelegatesAttribute(): Collection {
        return Delegate::whereIn('id', function ($q) {
            return $q->select('delegate_id')
                     ->from('moderators')
                     ->where('session_id', $this->id);
        })->get();
    }

    public function getStartAtAttribute($value): string {
        return $value ? $this->convert($value) : "";

    }

    public function getEndAtAttribute($value): string {
        return $value ? $this->convert($value) : "";
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

    public function setModerators(array $value): void {
        array_walk($value, function ($delegateId) {
            DB::table('moderators')->insert([
                "delegate_id" => $delegateId,
                "session_id"  => $this->id,
            ]);
        });
    }

    public function updateModerators(array $moderatorIds = null) {
        $data = [];

        if (!is_null($moderatorIds)) {
            foreach ($moderatorIds as $index => $id) {
                $data[$id] = ['order' => $index];
            }
        }

        $this->moderators()->sync($data);
    }

    public function formModeratorsAttribute() {
        return $this->moderators()->orderBy("pivot_order", 'desc')->get();
    }

    // Extra attributes
    public function getExtraAttributesAttribute(): SchemalessAttributes {
        return SchemalessAttributes::createForModel($this, 'extra_attributes');
    }

    public function scopeWithExtraAttributes(): Builder {
        return SchemalessAttributes::scopeWithSchemalessAttributes('extra_attributes');
    }

    public function scopeOrWithExtraAttributes(): Builder {
        return ExtraAttribute::scopeOrWithSchemalessAttributes('extra_attributes');
    }

}
