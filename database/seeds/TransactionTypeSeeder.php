<?php

use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $types = [
            'Credit Card',
            'Cheque',
            'Bank In',
            'Cash'
        ];

        foreach ($types as $type) {
            \App\TransactionType::firstOrCreate(['label' => $type], []);
        }
    }
}
