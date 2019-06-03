<?php

namespace App\Exports;

use App\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CheckinRecords implements FromCollection, WithHeadings, WithMapping
{
    private $query;
    /**
     * @var \App\Event
     */
    private $event;

    /**
     * CheckinRecords constructor.
     * @param \App\Event $event
     * @param            $query
     */
    public function __construct(Event $event, $query) {
        $this->query = $query;
        $this->event = $event;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
        return $this->query->get();
    }

    /**
     * @return array
     */
    public function headings(): array {
        return [
//            "Id",
            "Registration ID",
            "Delegate Name",
            "Email",
            'Institution',
            'Department',
            'Position',
            'Ticket',
            'Check In At',
        ];
    }

    public function map($record): array {
        return [
//            $record->id,
            (setting($this->event,
                    'registration_id_prefix') ?? "") . str_pad($record->registration_id,
                4, 0, STR_PAD_LEFT),
            sprintf("%s %s", $record->first_name, $record->last_name),
            $record->email,
            $record->institution,
            $record->department,
            $record->position,
            $record->ticket_name,
            $record->created_at,
        ];
    }


}
