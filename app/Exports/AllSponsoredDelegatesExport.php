<?php

namespace App\Exports;

use App\DelegateRole;
use App\Enums\DelegateDuplicationStatus;
use App\Enums\TransactionStatus;
use App\Event;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AllSponsoredDelegatesExport
    implements FromCollection, WithHeadings, WithMapping, Responsable
{
    use Exportable;

    private $fileName = 'sponsored_delegates.xlsx';
    private $event;


    /**
     * SponsoredDelegatesExport constructor.
     * @param \App\Event $event
     */
    public function __construct(Event $event) {
        $this->event = $event;
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
        return $this->event->sponsors->map->delegates->flatten()->values();
    }

    /**
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
            'Email',
            'Tel',
            'Fax',
            'Role',
            'Ticket',
            'Transaction Status',
            'Is Duplicated',
            'Sponsor Company',
            'Sponsor Correspondent Name',
            'Sponsor Correspondent Email',
            'Sponsor Correspondent Tel',
            'Sponsor Correspondent Address',
        ];
    }

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
            $sponsorRecord ? $sponsorRecord->sponsor->name : null,
            optional($sponsorRecord)->name,
            optional($sponsorRecord)->email,
            optional($sponsorRecord)->tel,
            optional($sponsorRecord)->address,
        ];
    }
}
