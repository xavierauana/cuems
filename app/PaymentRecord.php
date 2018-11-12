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
}
