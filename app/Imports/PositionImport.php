<?php

namespace App\Imports;

use App\Position;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PositionImport
    implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue
{

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection) {
        $collection->filter(function ($row) {
            return Validator::make($row->toArray(), [
                'position_name' => 'required',
            ])->passes();
        })
                   ->filter(function ($row) {
                       return Position::whereName($row['position_name'])
                                      ->count() === 0;
                   })
                   ->each(function ($row) {
                       Position::create([
                           'name' => $row['position_name'],
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
