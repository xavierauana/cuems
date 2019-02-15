<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $maleNames = json_decode(file_get_contents(storage_path("app/names_male.json")));

        foreach ($maleNames as $name) {
            DB::table('names')->insert([
                'name'    => $name,
                'is_male' => true,
            ]);
        }

        $femaleNames = json_decode(file_get_contents(storage_path("app/names_female.json")));

        foreach ($femaleNames as $name) {
            DB::table('names')->insert([
                'name'    => $name,
                'is_male' => false,
            ]);
        }

    }
}
