<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $fillable = [
        'name'
    ];

    protected $appends = [
        'urls',
    ];

    public function getUrlsAttribute() {
        return [
            'edit'   => route('institutions.edit', $this),
            'delete' => route('institutions.destroy', $this),
        ];
    }
}
