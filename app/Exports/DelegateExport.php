<?php

namespace App\Exports;

use App\DelegateRole;
use App\Enums\TransactionStatus;
use App\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DelegateExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @var \App\Event
     */
    private $event;
    private $transactionStatus;

    /**
     * DelegateExport constructor.
     * @param \App\Event $event
     */
    public function __construct(Event $event) {
        $this->event = $event;
        $this->transactionStatus = array_flip(TransactionStatus::getStatus());
    }

    /**
     * Excel header
     * @return array
     */
    public function headings(): array {
        return [
            'first_name',
            'last_name',
            'email',
            'mobile',
            'fax',
            'roles',
            'ticket',
            'transaction_status',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */

    public function collection() {
        return $this->event->delegates;
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($delegate): array {

        //                return [
        //                    'first_name'         => $delegate->first_name,
        //                    'last_name'          => $delegate->last_name,
        //                    'email'              => $delegate->email,
        //                    'mobile'             => $delegate->mobile,
        //                    'fax'                => $delegate->fax,
        //                    'roles'              => $delegate->roles->reduce(function (
        //                        string $carry, DelegateRole $role
        //                    ) {
        //                        return $carry .= $role->label . " ";
        //                    }, ""),
        //                    'ticket'             => $delegate->transactions->first()->ticket->name,
        //                    'transaction_status' => $this->transactionStatus[$delegate->transactions->first()->status],
        //                ];
        return [
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
        ];
    }
}
