<?php

namespace App\Exports;

use App\Institution;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InstitutionExport implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
        return Institution::all();
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array {
        return [
            $row->name
        ];
    }

    /**
     * @return array
     */
    public function headings(): array {
        return [
            'Name'
        ];
    }
}
