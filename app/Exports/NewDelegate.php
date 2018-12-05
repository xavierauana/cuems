<?php

namespace App\Exports;

use App\DelegateRole;
use App\Enums\TransactionStatus;
use App\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NewDelegate implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @var \App\Event
     */
    private $event;
    private $transactionStatus;

    public function __construct(Event $event) {
        $this->event = $event;
        $this->transactionStatus = array_flip(TransactionStatus::getStatus());
    }

    /**
     * param \Illuminate\Support\Collection
     */
    public function collection() {
        return $this->event->delegates()->whereIsVerified(false)->get();
    }

    public function headings(): array {
        return [
            'id',
            'first_name',
            'last_name',
            'email',
            'mobile',
            'fax',
            'roles',
            'ticket',
            'transaction_status',
            'is_duplicated',
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($delegate): array {
        return [
            $delegate->id,
            $delegate->first_name,
            $delegate->last_name,
            $delegate->email,
            $delegate->mobile,
            $delegate->fax,
            $delegate->roles->reduce(function (string $carry, DelegateRole $role
            ) {
                return $carry . $role->label . ", ";
            }, ""),
            $delegate->transactions->first()->ticket->name,
            $this->transactionStatus[$delegate->transactions->first()->status],
            $delegate->is_duplicated,
        ];
    }
}
