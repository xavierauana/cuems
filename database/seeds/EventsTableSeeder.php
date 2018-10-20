<?php

use App\Event;
use Illuminate\Database\Seeder;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $events = [
            [
                'title'    => "2018 Event",
                'start_at' => new \Carbon\Carbon("1 Dec 2018"),
                'end_at'   => new \Carbon\Carbon("3 Dec 2018"),
            ]
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
