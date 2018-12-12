<?php

namespace App;

use App\AbstractClasses\AbstractModelClass;
use Illuminate\Database\Eloquent\Relations\Relation;

class UploadFile extends AbstractModelClass
{

    protected $fillable = [

        'disk',
        'name',
        'path'
    ];

    // Relation

    public function event(): Relation {
        return $this->belongsTo(Event::class);
    }

    public function notifications(): Relation {
        return $this->belongsToMany(Notification::class);
    }


    // Helpers

    /**
     * @param array $params
     * @return array
     */
    public function getStoreRules(array $params = []): array {
        return [
            'file' => 'required|file|min:0'
        ];
    }
}
