<?php
/**
 * Author: Xavier Au
 * Date: 2019-01-25
 * Time: 10:54
 */

namespace App\Services;


use App\Delegate;
use App\DelegateRole;
use App\Enums\TransactionStatus;
use App\Notification;
use App\Ticket;
use App\Transaction;

class DummyDataCreator
{
    /**
     * @var \App\Delegate
     */
    private $delegate;
    /**
     * @var \App\Ticket
     */
    private $ticket;
    /**
     * @var \App\Transaction
     */
    private $transaction;
    /**
     * @var \App\DelegateRole
     */
    private $role;

    /**
     * @var \App\Notification
     */
    private $notification;
    /**
     * @var string
     */
    private $email;

    /**
     * @param \App\Notification $notification
     * @return DummyDataCreator
     */
    public function setNotification(Notification $notification
    ): DummyDataCreator {
        $this->notification = $notification;

        return $this;
    }


    public function createDummyData(): DummyDataCreator {
        $this->createDummyRole();
        $this->createDummyTicket();
        $this->createDummyDelegate();
        $this->createDummyTransaction();

        return $this;
    }


    private function createDummyTicket() {
        $this->ticket = factory(Ticket::class)->create([
            'event_id' => $this->notification->event_id
        ]);
    }

    private function createDummyRole() {
        $this->role = factory(DelegateRole::class)->create();
    }

    private function createDummyTransaction() {
        $this->transaction = factory(Transaction::class)->create([
            'ticket_id'  => $this->ticket->id,
            'payee_type' => get_class($this->delegate),
            'payee_id'   => $this->delegate->id,
            'status'     => TransactionStatus::COMPLETED
        ]);
    }

    /**
     * @throws \Exception
     */
    public function createDummyDelegate(): void {
        if (is_null($this->email)) {
            throw  new \Exception("Cannot create dummy delegate with out email");
        }
        $this->delegate = factory(Delegate::class)->create([
            'event_id' => $this->notification->event_id,
            'email'    => $this->email
        ]);
    }

    /**
     * @return \App\Delegate
     */
    public function getDelegate(): \App\Delegate {
        return $this->delegate;
    }

    /**
     * @return \App\Ticket
     */
    public function getTicket(): \App\Ticket {
        return $this->ticket;
    }

    /**
     * @return \App\Transaction
     */
    public function getTransaction(): \App\Transaction {
        return $this->transaction;
    }

    /**
     * @return \App\DelegateRole
     */
    public function getRole(): \App\DelegateRole {
        return $this->role;
    }

    /**
     * @param string $email
     * @return DummyDataCreator
     */
    public function setEmail(string $email): DummyDataCreator {
        $this->email = $email;

        return $this;
    }

    public function remove() {
        $type = $this->transaction->transactionType;
        $this->transaction->forceDelete();
        $type->forceDelete();
        $this->role->forceDelete();
        $this->delegate->forceDelete();
        $this->ticket->forceDelete();
    }
}