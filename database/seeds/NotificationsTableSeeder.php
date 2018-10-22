<?php

use App\Event;
use Illuminate\Database\Seeder;

class NotificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $event = Event::firstOrFail();
        $notifications = [
            [
                'name'           => "Welcome",
                'event'          => \App\Enums\SystemEvents::CREATE_DELEGATE,
                'template'       => "test",
                'from_name'      => "EMS",
                'from_email'     => "ems@anacreation.com",
                'include_ticket' => true,
                'subject'        => "Welcome to joining the event (self-registration)",
                'event_id'       => $event->id,
            ],
            [
                'name'       => "Welcome Admin Created",
                'event'      => \App\Enums\SystemEvents::ADMIN_CREATE_DELEGATE,
                'template'   => "test",
                'from_name'  => "EMS",
                'from_email' => "ems@anacreation.com",
                'subject'    => "Welcome to joining the event (admin-registration)",
                'event_id'   => $event->id,
            ],
        ];

        foreach ($notifications as $notification) {
            \App\Notification::create($notification);
        }

    }
}
