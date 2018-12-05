<?php

namespace App\Jobs;

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

        $goodData = collect($data)->filter(function ($item) {
            return !Validator::make($item, [
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
                "is_duplicated"      => "required|in:" . join(',',
                        array_keys(DelegateDuplicationStatus::getStatus())),
            ])->fails();
        })->each(function ($item) use (&$count) {

            if ($delegate = $this->event->delegates()->whereIsVerified(false)
                                        ->find($item['id'])) {
                $delegate->update($item);

                $delegate->is_verified = true;
                $delegate->is_duplicated = $item['is_duplicated'];

                $delegate->save();

                $count++;
            }
        });
    }
}
