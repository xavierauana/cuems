<?php

namespace App;

use App\Helpers\ExtraAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class Talk extends Model
{
    protected $fillable = [
        'title',
        'order',
        'extra_attributes',
    ];

    protected $casts = [
        'extra_attributes' => 'array',
    ];

    const StoreRules    = [
        'title'            => 'required',
        'speakers'         => 'required',
        'order'            => 'nullable|min:0|numeric',
        'extra_attributes' => 'nullable'
    ];
    const ErrorMessages = [];

    // Relation

    public function session(): Relation {
        return $this->belongsTo(Session::class);
    }


    // scope

    public function scopeSorted(Builder $query): Builder {
        $query->latest()->orderBy('order');
    }

    // Accessor

    public function getSpeakersAttribute(): Collection {
        return DB::table('speakers')->whereTalkId($this->id)
                 ->pluck('delegate_id');
    }

    public function getSpeakerDelegatesAttribute(): Collection {
        return Delegate::whereIn('id', function ($query) {
            return $query->select('delegate_id')
                         ->from('speakers')
                         ->whereTalkId($this->id);
        })->get();
    }

    // Helpers
    public function setSpeakers(array $speakerIds): void {
        array_walk($speakerIds, function ($speakerId) {
            DB::table('speakers')->insert([
                'delegate_id' => $speakerId,
                'talk_id'     => $this->id,
            ]);
        });
    }

    public function updateSpeakers(array $speakerIds): void {
        $oIds = DB::table('speakers')->whereTalkId($this->id)
                  ->pluck("delegate_id")->toArray();
        $nIds = $speakerIds;

        $removeIds = array_diff($oIds, $nIds);
        $addIds = array_diff($nIds, $oIds);

        DB::table('speakers')->whereTalkId($this->id)
          ->whereIn("delegate_id", $removeIds)->delete();

        array_walk($addIds, function ($speakerId) {
            DB::table('speakers')->insert([
                'delegate_id' => $speakerId,
                'talk_id'     => $this->id,
            ]);
        });
    }

    // Extra attributes
    public function getExtraAttributesAttribute(): SchemalessAttributes {
        return SchemalessAttributes::createForModel($this, 'extra_attributes');
    }

    public function scopeWithExtraAttributes(): Builder {
        return SchemalessAttributes::scopeWithSchemalessAttributes('extra_attributes');
    }

    public function scopeOrWithExtraAttributes(): Builder {
        return ExtraAttribute::scopeOrWithSchemalessAttributes('talks.extra_attributes');
    }
}
