<?php

namespace App\Jobs;

use App\Delegate;
use App\Enums\DelegateDuplicationStatus;
use App\Enums\TransactionStatus;
use App\Event;
use Closure;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateNewDelegates
 * @package App\Jobs
 */
class UpdateNewDelegates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $collection;
    public $event;

    /**
     * Create a new job instance.
     *
     * @param            $file
     * @param \App\Event $event
     */
    public function __construct($collection, Event $event) {
        //
        $this->collection = $collection;
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        $count = 0;
        $validator = function (array $item) {
            return Validator::make($item, [
                "id"                 => [
                    'required',
                    Rule::exists('delegates', 'id')
                        ->where(function ($query) {
                            $query->where('event_id', $this->event->id);
                        }),
                ],
                "registration_id"    => [
                    'required',
                    Rule::exists('delegates', 'registration_id')
                        ->where(function ($query) use ($item) {
                            return $query->where([
                                ['id', "=", $item['id']],
                                ['event_id', "=", $this->event->id],
                            ]);
                        }),
                ],
                "first_name"         => "required",
                "last_name"          => "required",
                "email"              => "required|email",
                "mobile"             => "required",
                "fax"                => "nullable",
                "department"         => "nullable",
                "institution"        => "nullable",
                "address_1"          => "required",
                "address_2"          => "nullable",
                "address_3"          => "nullable",
                "roles"              => "required",
                "ticket"             => "required",
                "transaction_status" => "required|in:" . join(',',
                        array_keys(TransactionStatus::getStatus())),
                "transaction_id"     => [
                    "nullable",
                    Rule::exists('transactions', 'charge_id')
                        ->where(function (Builder $query) use ($item) {
                            $query->where('payee_type', Delegate::class)
                                  ->where('payee_id', $item['id']);
                        })
                ],
                "is_duplicated"      => "required|in:" . join(',',
                        array_keys(DelegateDuplicationStatus::getStatus())),
            ]);
        };


        $validate = function (Collection $item) use ($validator) {
            $v = $validator($item->toArray());
            if ($v->fails()) {
                Log::info("{$item['id']}", [$v->errors()]);
            }


            return $v->passes();
        };

        $updateDelegate = function (Collection $item) use (&$count, $validator
        ) {
            $item = $item->toArray();;
            if ($delegate = $this->findDelegateByRegistrationId($item)) {
                $data = $validator($item)->validate();

                $delegate->update($data);
                $delegate->is_verified = true;
                $delegate->is_duplicated = $item['is_duplicated'];
                $delegate->save();
                $count++;
            }
        };

        $updateTransaction = function (Collection $item) {
            $item = $item->toArray();

            if ($delegate = $this->findDelegateByRegistrationId($item, true)) {
                $transaction = $delegate->transactions()
                                        ->whereChargeId($item['transaction_id'])
                                        ->first();
                $transaction->status = TransactionStatus::getStatus()[strtoupper($item['transaction_status'])];
                $transaction->save();
            }
        };

        DB::beginTransaction();

        try {
            $this->collection->first()
                             ->map($this->changeDelegateRegId())
                             ->filter($validate)
                             ->each($updateDelegate)
//                             ->tap(function ($collection) {
//                                 dd($collection);
//                             })
                             ->each($updateTransaction);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    /**
     * @param array       $importHeadings
     * @param string|null $prefix
     * @return \Closure
     */
    private function changeDelegateRegId(): Closure {
        $prefix = setting($this->event, 'registration_id_prefix') ?? "";

        return function (Collection $item) use ($prefix) {
            $item = $item->toArray();
            $item['registration_id'] = intval(str_replace($prefix, "",
                $item['registration_id']));

            return collect($item);
        };

    }

    /**
     * @param array $item
     * @param bool  $isVerified
     * @return \App\Delegate|null
     */
    private function findDelegateByRegistrationId(
        array $item, bool $isVerified = false
    ): ?Delegate {

        return $this->event->delegates()
                           ->whereIsVerified($isVerified)
                           ->find($item['id']);
    }
}
