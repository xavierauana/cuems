<?php
/**
 * Author: Xavier Au
 * Date: 2019-02-13
 * Time: 01:39
 */

namespace App\Services;


use App\Delegate;
use App\DelegateRole;
use App\Enums\SystemEvents;
use App\Enums\TransactionStatus;
use App\Event;
use App\Events\SystemEvent;
use App\Ticket;
use App\Transaction;
use App\TransactionType;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImportDelegateService
{

    private $user;
    private $collection;
    private $event;
    /**
     * @var \App\Transaction
     */
    private $transaction;
    /**
     * @var \App\Delegate
     */
    private $delegate;
    /**
     * @var \App\DelegateRole
     */
    private $role;
    /**
     * @var \App\Services\DelegateCreationService
     */
    private $service;

    /**
     * ImportDelegateService constructor.
     * @param \App\Transaction                      $transaction
     * @param \App\Delegate                         $delegate
     * @param \App\DelegateRole                     $role
     * @param \App\Services\DelegateCreationService $service
     */
    public function __construct(
        Transaction $transaction, Delegate $delegate, DelegateRole $role,
        DelegateCreationService $service
    ) {
        $this->transaction = $transaction;
        $this->delegate = $delegate;
        $this->role = $role;
        $this->service = $service;
    }


    /**
     * @param \App\Event                     $event
     * @param \Illuminate\Support\Collection $collection
     * @param \App\User                      $user
     * @throws \ReflectionException
     */
    public function create(Event $event, Collection $collection, User $user) {
        $this->user = $user;
        $this->event = $event;
        $this->collection = $collection;

        $rules = $this->getValidationRules();

        $this->conversion();

        $this->collection->filter(function ($item) use ($rules) {

            $validator = $this->getValidator($item, $rules);

            if ($validator->fails()) {
                Log::info("import validation failed",
                    (array)$validator->errors());

                return false;
            }


            return true;
        })
                         ->each(function ($data) {
                             $newDelegate = $this->service->adminImport($this->event,
                                 $data, true);
                             if ($newDelegate) {
                                 Log::info('fire event: ' . $newDelegate->name);

                                 event(new SystemEvent(SystemEvents::ADMIN_CREATE_DELEGATE,
                                     $newDelegate));
                             }
                         });
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    private function getValidationRules(): array {
        $rules = $this->delegate->getStoreRules();

        $rules = $rules = array_merge($rules, $this->transaction->getRules());

        return $rules;
    }

    private function conversion() {

        $this->collection = $this->collection
            ->map->toArray()
                 ->map(function ($data) {
                     return $this->dataMapping($data);
                 });
    }

    private function dataMapping(array $data): array {

        $roleIds = collect(explode(",", strtolower($data['role'] ?? "")))
            ->map(function (string $string) {
                return trim($string);
            })
            ->map(function (string $code) {
                return optional(DelegateRole::whereCode($code)->first())->id;
            })
            ->reject(null)->toArray();

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
        $new['roles_id'] = $roleIds;
        $new['ticket_id'] = optional(Ticket::whereEventId($this->event->id)
                                           ->whereCode($data['ticket_code'] ?? "")
                                           ->first())->id;
        $new['transaction_type_id'] = $data['transaction_type'] ? optional(TransactionType::whereLabel($data['transaction_type'])
                                                                                          ->first())->id : null;
        if ($data['sponsor_company'] ?? null) {
            $new['sponsor']['sponsor_id'] = optional($this->event->sponsors()
                                                                 ->firstOrCreate(['name' => $data['sponsor_company']]))->id;
        }

        $new['sponsor']['name'] = $data['sponsor_correspondent_name'] ?? null;
        $new['sponsor']['email'] = $data['sponsor_correspondent_email'] ?? null;
        $new['sponsor']['tel'] = $data['sponsor_correspondent_tel'] ?? null;
        $new['sponsor']['address'] = $data['sponsor_correspondent_address'] ?? null;

        return $new;
    }

    /**
     * @param array $data
     * @param array $rules
     * @return bool
     */
    private function getValidator(array $data, array $rules) {

        return Validator::make($data, $rules);
    }

}