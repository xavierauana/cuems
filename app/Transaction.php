<?php

namespace App;

use App\Contracts\SearchableModel;
use App\Enums\TransactionStatus;
use App\Traits\Searchable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Transaction
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Transaction extends Model implements SearchableModel
{
    use LogsActivity, Notifiable, Searchable;

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

    public $searchableColumns = [
        'transactions.status',
        'transactions.charge_id',
        'd.registration_id',
        'd.first_name',
        'd.last_name',
        'd.email',
        't.name',
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

    // Scope

    public function scopeJoinTables(Builder $query): Builder {
        return $query;

    }

    public function scopeForEvent(Builder $query, Event $event): Builder {
        return $query->select(['transactions.*', 'd.*', 't.name'])
                     ->join('delegates as d', 'transactions.payee_id', '=',
                         'd.id')
                     ->where('transactions.payee_type', Delegate::class)
                     ->join('tickets as t', 'transactions.ticket_id', '=',
                         't.id')
                     ->whereIn('transactions.ticket_id',
                         function ($query) use ($event) {
                             $query->select('id')
                                   ->from("tickets")
                                   ->where('event_id', $event->id);
                         });
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

    protected function keywordDRegistrationIdMutator(
        $keyword, Event $event = null
    ) {
        if (is_null($event)) {
            return $keyword;
        }

        $prefix = setting($event, 'registration_id_prefix') ?? "";
        $number = (int)str_replace($prefix, "", $keyword);

        return $number > 0 ? $number : $keyword;
    }

}
