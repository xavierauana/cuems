<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        $this->call(UsersTableSeeder::class);
        $this->call(EventsTableSeeder::class);
        $this->call(DelegateRolesTableSeeder::class);
        $this->call(InstitutionsTableSeeder::class);
        $this->call(NotificationsTableSeeder::class);
        $this->call(NamesTableSeeder::class);
        $this->call(PositionsTableSeeder::class);
        $this->call(SponsorsTableSeeder::class);
    }
}
