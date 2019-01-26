<?php

namespace App;

use Carbon\Carbon;
use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes, FormAccessible;

    protected $fillable = [
        'title',
        'end_at',
        'start_at',
    ];

    protected $casts = [
        'end_at'   => 'date',
        'start_at' => 'date',
    ];

    const StoreRules = [
        'title'    => 'required',
        'start_at' => 'required|date',
        'end_at'   => 'required|date|date_gt:start_at',
    ];

    const ValidationMessages = [
        'date_gt' => 'The end date must greater than start date.',
    ];

    // Relation
    public function delegates(): Relation {
        return $this->hasMany(Delegate::class);
    }

    public function sessions(): Relation {
        return $this->hasMany(Session::class);
    }

    public function tickets(): Relation {
        return $this->hasMany(Ticket::class);
    }

    public function notifications(): Relation {
        return $this->hasMany(Notification::class);
    }

    public function templates(): Relation {
        return $this->hasMany(Template::class);
    }

    public function settings(): Relation {
        return $this->hasMany(Setting::class);
    }

    public function expenses(): Relation {
        return $this->hasMany(Expense::class);
    }

    public function sponsors(): Relation {
        return $this->hasMany(Sponsor::class);
    }

    public function uploadFiles(): Relation {
        return $this->hasMany(UploadFile::class);
    }

    public function paymentRecords(): Relation {
        return $this->hasMany(PaymentRecord::class);
    }

    // Helpers

    public function getTotalExpense(): float {
        $total = $this->expenses()->sum('amount');

        return (float)($total ?? 0);
    }

    // Accessor
    public function formEndAtAttribute($value) {
        return (new Carbon($value))->format("d M Y");
    }

    public function formStartAtAttribute($value) {
        return (new Carbon($value))->format("d M Y");
    }

    // Mutation
    public function setEndAtAttribute($value) {
        if (!$value instanceof Carbon) {
            $this->attributes['end_at'] = Carbon::createFromFormat('d M Y',
                $value);
        } else {
            $this->attributes['end_at'] = $value;
        }

    }

    public function setStartAtAttribute($value) {
        if (!$value instanceof Carbon) {
            $this->attributes['start_at'] = Carbon::createFromFormat('d M Y',
                $value);
        } else {
            $this->attributes['start_at'] = $value;
        }

    }

}
