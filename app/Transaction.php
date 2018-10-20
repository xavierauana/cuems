<?php

namespace App;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use LogsActivity;

    use Notifiable;

    protected $fillable = [
        'charge_id',
        'card_brand',
        'last_4',
        'ticket_id',
        'status',
        'note',
    ];

    protected static $logAttributes = [
        'charge_id',
        'card_brand',
        'last_4',
        'ticket_id',
        'status',
        'note',
    ];

    protected static $logOnlyDirty = true;

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getRules(): array {
        return [
            'status'    => 'required|in:' . implode(",",
                    array_values((new \ReflectionClass(TransactionStatus::class))->getConstants())),
            'ticket_id' => 'required|in:' . implode(",",
                    Ticket::pluck('id')->toArray()),
            'note'      => 'nullable'
        ];
    }


    // Relation

    public function payee(): Relation {
        return $this->morphTo();
    }

    public function ticket(): Relation {
        return $this->belongsTo(Ticket::class);
    }

    public function routeNotificationForMail(): string {
        return $this->payee->email;
    }
}
