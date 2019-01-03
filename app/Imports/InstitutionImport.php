<?php

namespace App\Imports;

use App\Institution;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InstitutionImport
    implements ToCollection, ShouldQueue, WithChunkReading, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection) {
        $collection->filter(function ($row) {
            return Validator::make($row->toArray(), [
                'name' => 'required',
            ])->passes();
        })
                   ->filter(function ($row) {
                       return Institution::whereName($row['name'])
                                         ->count() === 0;
                   })
                   ->each(function ($row) {
                       Institution::create([
                           'name' => $row['name'],
                       ]);
                   });
    }

    public function headingRow(): int {
        return 1;
    }

    /**
     * @return int
     */
    public function chunkSize(): int {
        return 500;
    }
}
