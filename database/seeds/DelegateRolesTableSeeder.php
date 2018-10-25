<?php

use Illuminate\Database\Seeder;

class DelegateRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $roles = [
            [
                'label'      => 'Default',
                'code'       => 'default',
                'is_default' => true
            ],
            [
                'label' => 'Speaker',
                'code'  => 'speaker',
            ],
            [
                'label' => 'Honorary',
                'code'  => 'honorary',
            ],
            [
                'label' => 'Committee',
                'code'  => 'committee',
            ],
            [
                'label' => 'Chairperson',
                'code'  => 'chairperson',
            ],
        ];

        foreach ($roles as $role) {
            \App\DelegateRole::create($role);
        }
    }
}
