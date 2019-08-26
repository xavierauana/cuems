<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Advertisement extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $fillable = [
        'description',
        'buyer',
        'type_id',
    ];

    public static function StoreRules() {
        return [
            'buyer'       => 'required',
            'logo'        => 'nullable|file',
            'banners.*'   => 'nullable|file',
            'description' => 'nullable',
            'type_id'     => 'required|exists:advertisement_types,id',
        ];
    }


    // Relation
    public function event(): BelongsTo {
        return $this->belongsTo(Event::class);
    }

    public function type(): BelongsTo {
        return $this->belongsTo(AdvertisementType::class);
    }


    public function registerMediaCollections() {

        $this->addMediaCollection('logo')->singleFile();

        $this->addMediaCollection('banners');
    }
}
