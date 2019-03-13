<?php

namespace App;

use App\Enums\TransactionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use LogsActivity, Notifiable;

    protected $casts = [
        'created_at' => 'datetime'
    ];

    protected $fillable = [
        'charge_id',
        'card_brand',
        'last_4',
        'ticket_id',
        'transaction_type_id',
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
        'transaction_type_id'
    ];

    protected static $logOnlyDirty = true;

    protected $appends = [
        'uuid'
    ];

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getRules(): array {
        return [
            'status'              => 'required|in:' . implode(",",
                    array_values((new \ReflectionClass(TransactionStatus::class))->getConstants())),
            'ticket_id'           => 'required|in:' . implode(",",
                    Ticket::pluck('id')->toArray()),
            'note'                => 'nullable',
            'transaction_type_id' => 'required|exists:transaction_types,id'
        ];
    }

    // Relation

    public function transactionType(): Relation {
        return $this->belongsTo(TransactionType::class);
    }

    public function payee(): Relation {
        return $this->morphTo();
    }

    public function ticket(): Relation {
        return $this->belongsTo(Ticket::class);
    }

    // Helpers
    public function routeNotificationForMail(): string {
        return $this->payee->email;
    }

    public function getUuidAttribute(): string {
        return base64_encode(serialize([
            'transaction_id' => $this->id,
            'ticket_id'      => $this->ticket->id,
            'event_id'       => $this->ticket->event->id,
            'delegate_id'    => $this->payee->id,
        ]));
    }

    public function parseUuid($data): array {

        return unserialize(base64_decode($data));
    }

    public function checkIn(User $user) {
        DB::table('check_in')->insert([
            'transaction_id' => $this->id,
            'created_at'     => Carbon::now(),
            'user_id'        => $user->id
        ]);
    }

    public function getCheckInRecords(): array {
        return DB::table("check_in")->latest()
                 ->where('transaction_id', $this->id)
                 ->get()->map(function ($record) {
                return [
                    'timestamp' => $record->created_at,
                    'user'      => User::find($record->user_id),
                ];
            })->toArray();
    }
}
