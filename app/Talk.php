<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Talk extends Model
{
    protected $fillable = [
        'title',
        'order'
    ];

    const StoreRules    = [
        'title'    => 'required',
        'speakers' => 'required',
        'order'    => 'nullable|min:0|numeric',
    ];
    const ErrorMessages = [];


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
}
