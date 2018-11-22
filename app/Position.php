<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'name'
    ];

    protected $appends = [
        'urls',
    ];

    public function getUrlsAttribute() {
        return [
            'edit'   => route('positions.edit', $this),
            'delete' => route('positions.destroy', $this),
        ];
    }
}
