<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Vendor extends Model
{

    protected $fillable = [
        'name'
    ];

    protected $appends = [
        'urls',
    ];

    // Relation
    public function expenses(): Relation {
        return $this->hasMany(Expense::class);
    }

    // Accessor
    public function getUrlsAttribute() {
        return [
            'edit'   => route('vendors.edit', $this),
            'delete' => route('vendors.destroy', $this),
        ];
    }
}
