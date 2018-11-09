<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Expense extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $fillable = [
        'amount',
        'note',
        'date',
        'vendor_id',
        'category_id',
    ];

    protected $casts = [
        'date' => 'date'
    ];

    // Relation

    public function event(): Relation {
        return $this->belongsTo(Event::class);
    }

    public function category(): Relation {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function vendor(): Relation {
        return $this->belongsTo(Vendor::class);
    }

    public function files(): Relation {
        return $this->hasMany(ExpenseMedium::class);
    }

    // Accessor

    public function getVendorNameAttribute(): ?String {
        return $this->vendor->name;
    }

    public function getCategoryNameAttribute(): ?String {
        return $this->category->name;
    }
}
