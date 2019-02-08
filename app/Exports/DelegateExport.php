<?php

namespace App\Exports;

use App\DelegateRole;
use App\Enums\DelegateDuplicationStatus;
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
     * @var string
     */
    private $delegateType;

    /**
     * DelegateExport constructor.
     * @param \App\Event $event
     */
    public function __construct(Event $event, $delegateType = 'default') {
        $this->event = $event;
        $this->transactionStatus = array_flip(TransactionStatus::getStatus());
        $this->delegateType = $delegateType;
    }

    /**
     * Excel header
     * @return array
     */
    public function headings(): array {
        return [
            'Registration Id',
            'Title',
            'Gender',
            'Surname',
            'Given Name',
            'Position',
            'Department',
            'Institution / Hospital',
            'Address Line 1',
            'Address Line 2',
            'Address Line 3',
            'Country',
            'Email',
            'Tel',
            'Fax',
            'Role',
            'Ticket',
            'Transaction Status',
            'Is Duplicated',
            'Duplicated With',
            'Sponsor Company',
            'Sponsor Correspondent Name',
            'Sponsor Correspondent Email',
            'Sponsor Correspondent Tel',
            'Sponsor Correspondent Address',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */

    public function collection() {
        $query = $this->event->delegates();

        switch ($this->delegateType) {
            case 'duplicated':
                return $query->where('is_duplicated', '=',
                    DelegateDuplicationStatus::DUPLICATED)->get();
            default:
                return $query->where('is_duplicated', '<>',
                    DelegateDuplicationStatus::DUPLICATED)->get();
        }
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($delegate): array {

        $sponsorRecord = $delegate->sponsorRecord;

        return [
            $delegate->getRegistrationId(),
            $delegate->prefix,
            $delegate->is_male ? "male" : "female",
            $delegate->last_name,
            $delegate->first_name,
            $delegate->position,
            $delegate->department,
            $delegate->institution,
            $delegate->address_1,
            $delegate->address_2,
            $delegate->address_3,
            $delegate->country,
            $delegate->email,
            $delegate->mobile,
            $delegate->fax,
            $delegate->roles->reduce(function (string $carry, DelegateRole $role
            ) {
                return $carry . $role->label . ", ";
            }, ""),
            $delegate->transactions->first()->ticket->name,
            TransactionStatus::getStatusKey($delegate->transactions->first()->status),
            $delegate->is_duplicated == DelegateDuplicationStatus::DUPLICATED ? "DUPLICATED" : "NA",
            $delegate->duplicated_with,
            $sponsorRecord ? $sponsorRecord->sponsor->name : null,
            optional($sponsorRecord)->name,
            optional($sponsorRecord)->email,
            optional($sponsorRecord)->tel,
            optional($sponsorRecord)->address,
        ];
    }
}
