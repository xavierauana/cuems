<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PaymentRecord extends Model
{
    use LogsActivity;

    protected $fillable = [
        'status',
        'invoice_id',
        'form_data',
    ];

    protected static $logAttributes = [
        'status',
    ];

    protected static $logOnlyDirty = true;

    public function setFormDataAttribute($value) {
        $this->attributes['form_data'] = encrypt($value);
    }

    public function getFormDataAttribute($value) {
        return decrypt($value);
    }
}
