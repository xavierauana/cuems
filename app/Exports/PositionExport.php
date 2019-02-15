<?php

namespace App\Exports;

use App\Position;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PositionExport implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
        $c = Position::all();

        return $c;
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array {
        return [
            $row->name,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array {
        return [
            'Position Name'
        ];
    }
}
