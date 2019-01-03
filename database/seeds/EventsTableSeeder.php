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
                'title'    => "2019 Event",
                'start_at' => new \Carbon\Carbon("01 May 2019"),
                'end_at'   => new \Carbon\Carbon("03 May 2019"),
            ]
        ];

        foreach ($events as $event) {
            $newEvent = Event::create($event);

            foreach (config('event.settings', []) as $key) {
                $newEvent->settings()->create(compact('key'));
            }
        }
    }
}
