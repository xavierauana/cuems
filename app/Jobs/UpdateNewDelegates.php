<?php

namespace App\Jobs;

use App\Delegate;
use App\Enums\DelegateDuplicationStatus;
use App\Enums\TransactionStatus;
use App\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

        $validate = function ($item) {
            $validator = Validator::make($item->toArray(), [
                "id"                 => [
                    'required',
                    Rule::exists('delegates')->where(function ($query) {
                        $query->where('event_id', $this->event->id);
                    }),
                ],
                "first_name"         => "required",
                "last_name"          => "required",
                "email"              => "required|email",
                "mobile"             => "required",
                "fax"                => "nullable",
                "roles"              => "required",
                "ticket"             => "required",
                "transaction_status" => "required|in:" . join(',',
                        array_keys(TransactionStatus::getStatus())),
                "transaction_id"     => [
                    "nullable",
                    Rule::exists('transactions', 'charge_id')
                        ->where(function ($query) use ($item) {
                            $query->where([
                                ['payee_type', '=', Delegate::class],
                                ['payee_id', '=', $item['id']],
                            ]);
                        })
                ],
                "is_duplicated"      => "required|in:" . join(',',
                        array_keys(DelegateDuplicationStatus::getStatus())),
            ]);


            return $validator->passes();
        };

        $update = function ($item) use (&$count) {
            $item = $item->toArray();
            if ($delegate = $this->event->delegates()
                                        ->whereIsVerified(false)
                                        ->find($item['id'])) {
                $delegate->update($item);

                $delegate->is_verified = true;
                $delegate->is_duplicated = $item['is_duplicated'];

                $delegate->save();

                $transaction = $delegate->transactions()
                                        ->whereChargeId($item['transaction_id'])
                                        ->first();
                $transaction->status = TransactionStatus::getStatus()[strtoupper($item['transaction_status'])];
                $transaction->save();
                $count++;
            }
        };

        $this->collection->first()->filter($validate)->each($update);
    }
}
