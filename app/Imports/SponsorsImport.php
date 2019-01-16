<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SponsorsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     * @return mixed
     */
    public function collection(Collection $collection) {
    }
}
