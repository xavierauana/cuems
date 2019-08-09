<?php

namespace App;

use Carbon\Carbon;
use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Database\Eloquent\Builder as BuilderAlias;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Event
 * @package App
 * @mixin BuilderAlias
 */
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
    public function positionGroupings(): HasMany {
        return $this->hasMany(PositionGrouping::class);
    }

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

    public function sponsorRecords(): Relation {
        return $this->hasMany(SponsorRecord::class);
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

    /**
     * Get joined query check_in, transactions and ticket,
     * filter by event_id
     * @param bool $uniqueTransaction
     * @return \App\Delegate|\Illuminate\Database\Query\Builder
     */
    public function getCheckinJoinQuery() {

        return Delegate::where('delegates.event_id', $this->id)
                       ->join('transactions', 'delegates.id', '=',
                           'transactions.payee_id')
                       ->where('transactions.payee_type', Delegate::class)
                       ->join('check_in', 'check_in.transaction_id', '=',
                           'transactions.id')
                       ->join('tickets', 'tickets.id', '=',
                           'transactions.ticket_id')
                       ->joinSub(function ($query) {

                           $part = config('database.default') === 'sqlite' ?
                               "transaction_id || DATE(created_at)" :
                               "CONCAT(transaction_id, DATE(created_at))";

                           $str = 'transaction_id_date';
                           $sql = "{$part} {$str}, MIN(id) first";
                           $query->selectRaw($sql)
                                 ->from('check_in')
                                 ->groupBy('' . $str . '');
                       }, 'temp', 'temp.first', '=', 'check_in.id');

    }

    public function getCheckInCount(): int {
        return $this->getCheckinJoinQuery()
                    ->count();
    }

    public function getExportCheckinFilename(): string {
        return $this->name . "_checkin_records_" . Carbon::now()
                                                         ->toDateTimeString() . '.xlsx';

    }

    public function getCheckinControllerQuery(
        string $keyword = null, string $filterDate = null,
        bool $uniqueTransaction = false
    ) {

        $query = $this->getCheckinJoinQuery($uniqueTransaction)
                      ->orderBy('check_in.created_at', 'desc');

        if ($filterDate and ($date = new Carbon($filterDate))) {
            $query->whereDate('check_in.created_at', $date->toDateString());
        }

        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('delegates.first_name', 'like', "%{$keyword}%")
                      ->orWhere('delegates.last_name', 'like', "%{$keyword}%")
                      ->orWhere('delegates.email', 'like', "%{$keyword}%")
                      ->orWhere('tickets.name', 'like', "%{$keyword}%");
            });
        }


        return $query;
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
