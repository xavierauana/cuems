<?php

namespace App\Exports;

use App\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PositionGroupingExport
    implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @var \App\Event
     */
    private $event;

    public function __construct(Event $event) {
        $this->event = $event;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
        return $this->event->positionGroupings;
    }

    /**
     * @return array
     */
    public function headings(): array {
        return [
            'position',
            'grouping'
        ];
    }

    /**
     * @param \App\PositionGrouping $grouping
     * @return array
     */
    public function map($grouping): array {
        return [
            $grouping->position,
            $grouping->grouping,
        ];
    }
}
