<?php
/**
 * Author: Xavier Au
 * Date: 2018-12-28
 * Time: 09:41
 */

namespace App\Services;


use App\Contracts\DuplicateCheckerInterface;
use App\Delegate;
use App\DelegateRole;
use App\Enums\DelegateDuplicationStatus;
use App\Enums\PaymentRecordStatus;
use App\Enums\TransactionStatus;
use App\Event;
use App\PaymentRecord;
use Illuminate\Support\Facades\DB;

class DelegateCreationService
{

    private $role;

    private $user;
    /**
     * @var \App\Services\DelegateDuplicateChecker
     */
    private $checker;

    /**
     * DelegateCreationService constructor.
     * @param \App\DelegateRole                        $role
     * @param \App\Contracts\DuplicateCheckerInterface $checker
     */
    public function __construct(
        DelegateRole $role, DuplicateCheckerInterface $checker
    ) {
        $this->role = $role;
        $this->user = request()->user();
        $this->checker = $checker;
    }

    public function adminCreate(
        Event $event, array $data, bool $checkDuplicate = false
    ): Delegate {

        $data = $this->addRegistrationId($event, $data);

        $transactionData = [
            'status'    => $data['status'],
            'ticket_id' => $data['ticket_id'],
            'note'      => $data['note'],
        ];

        $code = ($data['role'] ?? null);

        DB::beginTransaction();

        try {

            $newDelegate = $this->baseCreate($event, $data, $code,
                $transactionData);

            $this->createDelegateSponsor($data, $newDelegate);

            $this->recordAdminActivity($newDelegate);

            $this->markDelegateIsVerified($newDelegate);

            DB::commit();

            return $newDelegate;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function import() {
        //TODO: Implement method
    }

    /**
     * @param \App\Event $event
     * @param array      $data
     * @param bool       $checkDuplicate
     * @return \App\Delegate
     * @throws \Exception
     */
    public function create(
        Event $event, array $data, bool $checkDuplicate = false
    ): Delegate {
        DB::beginTransaction();

        try {
            $data = $this->addRegistrationId($event, $data);

            /** @var \App\Delegate $newDelegate */
            $newDelegate = $this->createDelegate($event, $data);

            $role = isset($data['role']) ?
                $this->role->where('code', $data['role'])->first() :
                $this->role->whereIsDefault(true)->firstOrFail();

            $newDelegate->roles()->save($role);

            $newDelegate->transactions()->create($data);

            $this->createDelegateSponsor($data, $newDelegate);

            $this->recordAdminActivity($newDelegate);

            if ($checkDuplicate) {
                $this->checkDuplicated($event, $newDelegate);
            }
            DB::commit();

            return $newDelegate;

        } catch (\Exception $exception) {

            DB::rollBack();

            throw $exception;

        }
    }

    public function selfCreate(
        Event $event, array $data, $chargeResponse, string $refId
    ): Delegate {

        $data = $this->addRegistrationId($event, $data);

        $transactionData = [
            'charge_id'  => $chargeResponse->chargeID,
            'card_brand' => $chargeResponse->brand,
            'last_4'     => $chargeResponse->last4,
            'ticket_id'  => $data['ticket_id'],
            'status'     => TransactionStatus::AUTHORIZED,
        ];


        DB::beginTransaction();

        try {


            $delegate = $this->baseCreate($event, $data, null,
                $transactionData);

            $this->checkDuplicated($event, $delegate);

            PaymentRecord::findOrFail($refId)
                         ->update(['status' => PaymentRecordStatus::AUTHORIZED]);

            DB::commit();

            return $delegate;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    public function checkDuplicated(Event $event, Delegate $newDelegate): void {

        $this->checker = $this->checker->setEvent($event);


        $emailDuplicated = $this->checker->find('email', $newDelegate->email);
        $mobileDuplicated = $this->checker->find('email', $newDelegate->email);
        if ($emailDuplicated->count() > 1 or $mobileDuplicated->count() > 1) {
            $first = null;
            $first = $emailDuplicated->first(function ($i) use ($newDelegate) {
                return $i->id !== $newDelegate->id;
            });

            if (is_null($first)) {
                $first = $mobileDuplicated->first(function ($i) use (
                    $newDelegate
                ) {
                    return $i->id !== $newDelegate->id;
                });
            }

            $newDelegate->is_duplicated = DelegateDuplicationStatus::DUPLICATED;
            $newDelegate->duplicated_with = $first->getRegistrationId();
        } else {
            $newDelegate->is_duplicated = DelegateDuplicationStatus::NO;
        }

        $newDelegate->save();
    }

    private function addRegistrationId(Event $event, array $data): array {
        $data['registration_id'] = ($event->delegates()
                                          ->max('registration_id') ?? 0) + 1;

        return $data;
    }

    private function getNewDelegateRole(string $code = null): DelegateRole {

        $role = $code ?
            $this->role->where('code', $code)->firstOrFail() :
            $this->role->whereIsDefault(true)->firstOrFail();

        return $role;
    }

    /**
     * @param \App\Event $event
     * @param array      $data
     * @return \App\Delegate
     */
    private function createDelegate(Event $event, array $data): \App\Delegate {
        /** @var \App\Delegate $newDelegate */
        $newDelegate = $event->delegates()->create($data);

        return $newDelegate;
    }

    /**
     * @param \App\Delegate $newDelegate
     * @param               $code
     */
    private function assignRoleToDelegate(
        Delegate $newDelegate, string $code = null
    ): void {
        $newDelegate->roles()
                    ->save($this->getNewDelegateRole($code));
    }

    /**
     * @param \App\Delegate $newDelegate
     * @param array         $transactionData
     */
    private function createDelegateTransaction(
        Delegate $newDelegate, array $transactionData
    ): void {
        $newDelegate->transactions()->create($transactionData);
    }

    /**
     * @param \App\Event  $event
     * @param array       $data
     * @param string|null $roleCode
     * @param array       $transactionData
     * @return \App\Delegate
     */
    private function baseCreate(
        Event $event, array $data, string $roleCode = null,
        array $transactionData
    ): Delegate {

        $newDelegate = $this->createDelegate($event, $data);

        $this->assignRoleToDelegate($newDelegate, $roleCode);

        $this->createDelegateTransaction($newDelegate, $transactionData);

        DB::commit();

        return $newDelegate;

    }

    /**
     * @param array         $data
     * @param \App\Delegate $newDelegate
     */
    private function createDelegateSponsor(array $data, Delegate $newDelegate
    ): void {
        if (isset($data['sponsor']['sponsor_id'])) {
            $sponsorData = $data['sponsor'];

            $newDelegate->sponsorRecord()->create($sponsorData);
        }
    }

    /**
     * @param \App\Delegate $newDelegate
     */
    private function recordAdminActivity(Delegate $newDelegate): void {
        DB::table('delegate_creations')->insert([
            'delegate_id' => $newDelegate->id,
            'user_id'     => $this->user->id
        ]);
    }

    private function markDelegateIsVerified(Delegate $newDelegate) {
        $newDelegate->is_verified = true;
        $newDelegate->save();
    }
}