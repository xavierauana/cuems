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

    public function contacts(): Relation {
        return $this->hasMany(VendorContact::class);
    }

    // Accessor
    public function getUrlsAttribute() {
        return [
            'edit'     => route('vendors.edit', $this),
            'delete'   => route('vendors.destroy', $this),
            'contacts' => route('vendors.vendorContacts.index', $this),
        ];
    }
}
