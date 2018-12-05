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

        $headers = [];

        $data = [];

        $count = 0;

        $this->collection->first()->each(function ($row, $index) use (
            &$headers, &$data
        ) {
            if ($index === 0) {
                $headers = $row->toArray();
            } else {
                $data[] = array_combine($headers, $row->toArray());
            }
        });

        $validate = function ($item) {
            $validator = Validator::make($item, [
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
                    "required",
                    Rule::exists('transactions', 'charge_id')->where(function (
                        $query
                    ) use (
                        $item
                    ) {
                        $query->where([
                            ['payee_type', '=', Delegate::class],
                            ['payee_id', '=', $item['id']],
                        ]);
                    })
                ],
                "is_duplicated"      => "required|in:" . join(',',
                        array_keys(DelegateDuplicationStatus::getStatus())),
            ]);

            return !$validator->fails();
        };

        $update = function ($item) use (&$count) {

            if ($delegate = $this->event->delegates()->whereIsVerified(false)
                                        ->find($item['id'])) {
                $delegate->update($item);

                $delegate->is_verified = true;
                $delegate->is_duplicated = $item['is_duplicated'];

                $delegate->save();

                $transaction = $delegate->transactions()
                                        ->whereChargeId($item['charge_id'])
                                        ->first();
                $transaction->status = TransactionStatus::getStatus()[strtoupper($item['transaction_status'])];
                $transaction->save();

                $count++;
            }
        };

        collect($data)->filter($validate)->each($update);
    }
}
