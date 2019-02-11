<?php

namespace App;

use App\Traits\FlatpickrConversion;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Session extends Model
{
    use FlatpickrConversion;

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
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    const StoreRules    = [
        'title'           => 'required',
        'venue'           => 'nullable',
        'subtitle'        => 'nullable',
        'sponsor'         => 'nullable',
        'moderation_type' => 'nullable|numeric',
        'moderators'      => 'nullable',
        'start_at'        => 'required|date',
        'end_at'          => 'required|date|date_gt:start_at',
        'order'           => 'nullable|numeric|min:0',
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

    public function getModeratorsAttribute(): Collection {
        return DB::table('moderators')
                 ->where('session_id', $this->id)
                 ->pluck('delegate_id');
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

    public function updateModerators(array $moderatorIds) {
        $oIds = DB::table('moderators')
                  ->whereSessionId($this->id)
                  ->pluck("delegate_id")->toArray();
        $nIds = $moderatorIds;

        $removeIds = array_diff($oIds, $nIds);
        $addIds = array_diff($nIds, $oIds);

        DB::table('moderators')->whereSessionId($this->id)
          ->whereIn("delegate_id", $removeIds)->delete();

        array_walk($addIds, function ($moderatorId) {
            DB::table('moderators')->insert([
                'delegate_id' => $moderatorId,
                'session_id'  => $this->id,
            ]);
        });
    }


}
