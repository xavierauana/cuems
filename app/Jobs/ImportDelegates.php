<?php

namespace App\Jobs;

use App\Delegate;
use App\DelegateRole;
use App\Enums\DelegateDuplicationStatus;
use App\Enums\SystemEvents;
use App\Enums\TransactionStatus;
use App\Event;
use App\Events\SystemEvent;
use App\Services\DelegateDuplicateChecker;
use App\Ticket;
use App\Transaction;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImportDelegates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var \App\Event
     */
    private $event;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $collection;
    /**
     * @var \App\User
     */
    private $user;


    /**
     * Create a new job instance.
     *
     * @param \App\Event                     $event
     * @param \Illuminate\Support\Collection $collection
     */
    public function __construct(Event $event, Collection $collection, User $user
    ) {
        //
        $this->event = $event;

        $this->collection = $collection;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @param \App\Transaction $transaction
     * @return void
     * @throws \ReflectionException
     */
    public function handle(
        Transaction $transaction, Delegate $delegate, DelegateRole $role
    ) {

        $rules = $this->getValidationRules($transaction, $delegate, $role);


        $this->conversion();

        $collection = $this->collection->filter(function ($item) use ($rules) {
            return !!$this->validateData($item, $rules);
        });

        $collection->each(function ($data) use ($role) {

            DB::beginTransaction();

            try {
                $newDelegate = $this->event->delegates()
                                           ->create($data);

                $newDelegate->roles()->save($role->where('code',
                    $data['role'])->first());

                $newDelegate->transactions()->create($data);

                $newDelegate->transactions()->create($data);

                if (isset($data['sponsor']['sponsor_id'])) {
                    $sponsorData = $data['sponsor'];

                    $newDelegate->sponsorRecord()->create($sponsorData);
                }

                DB::table('delegate_creations')->insert([
                    'delegate_id' => $newDelegate->id,
                    'user_id'     => $this->user->id
                ]);

                DB::commit();

                $this->checkDuplicated($newDelegate);

                Log::info('fire event: ' . $newDelegate->name);

                sleep(0.5);
                event(new SystemEvent(SystemEvents::ADMIN_CREATE_DELEGATE,
                    $newDelegate));

            } catch (\Exception $exception) {

                DB::rollBack();

                throw $exception;

            }
        });

    }

    /**
     * @param $this
     * @param $newDelegate
     */
    function checkDuplicated($newDelegate): void {
        $checker = new DelegateDuplicateChecker($this->event);

        if ($checker->isDuplicated(['email', 'mobile'],
            [$newDelegate->email, $newDelegate->mobile])) {
            $newDelegate->is_duplicated = DelegateDuplicationStatus::DUPLICATED;
        } else {
            $newDelegate->is_duplicated = DelegateDuplicationStatus::NO;
        }

        $newDelegate->is_verified = true;

        $newDelegate->save();
    }

    private function validateData(array $data, array $rules): ?array {

        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            return $validator->validate();
        }

        return null;
    }

    /**
     * @param \App\Transaction  $transaction
     * @param \App\Delegate     $delegate
     * @param \App\DelegateRole $role
     * @return array
     * @throws \ReflectionException
     */
    private function getValidationRules(
        Transaction $transaction, Delegate $delegate, DelegateRole $role
    ): array {
        $rules = $delegate->getStoreRules();

        $rules = array_merge($rules, [
            'role' => 'required|in:' . implode(',',
                    $role->pluck('code')->toArray())
        ]);
        $rules = $rules = array_merge($rules, $transaction->getRules());

        return $rules;
    }

    private function conversion() {

        $this->collection = $this->collection
            ->map(function ($item) {
                $data = $item->toArray();

                return $data;
            })
            ->map(function ($data) {

                $new = [];
                $new['prefix'] = $data['title'] ?? null;
                $new['first_name'] = $data['given_name'] ?? null;
                $new['last_name'] = $data['surname'] ?? null;
                $new['is_male'] = strtolower($data['gender'] ?? "") == 'male';
                $new['position'] = $data['position'] ?? null;
                $new['department'] = $data['department'] ?? null;
                $new['institution'] = $data['institution_hospital'] ?? null;
                $new['address_1'] = $data['address_line_1'] ?? null;
                $new['address_2'] = $data['address_line_2'] ?? null;
                $new['address_3'] = $data['address_line_3'] ?? null;
                $new['country'] = $data['country'] ?? null;
                $new['email'] = $data['email'] ?? null;
                $new['mobile'] = $data['tel'] ?? null;
                $new['fax'] = $data['fax'] ?? null;

                $states = array_keys(TransactionStatus::getStatus());
                $new['status'] = in_array($data['transaction_status'] ?? "",
                    $states) ? (TransactionStatus::getStatus())[$data['transaction_status']] : "";
                $new['role'] = optional(DelegateRole::whereCode(strtolower($data['role'] ?? ""))
                                                    ->first())->code;
                $new['ticket_id'] = optional(Ticket::whereCode($data['ticket_code'] ?? "")
                                                   ->first())->id;
                if ($data['sponsor_company'] ?? null) {
                    $new['sponsor']['sponsor_id'] = optional($this->event->sponsors()
                                                                         ->firstOrCreate(['name' => $data['sponsor_company']]))->id;
                }

                $new['sponsor']['name'] = $data['sponsor_correspondent_name'] ?? null;
                $new['sponsor']['email'] = $data['sponsor_correspondent_email'] ?? null;
                $new['sponsor']['tel'] = $data['sponsor_correspondent_tel'] ?? null;
                $new['sponsor']['address'] = $data['sponsor_correspondent_address'] ?? null;

                return $new;

            });
    }
}
