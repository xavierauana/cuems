<?php
/**
 * Author: Xavier Au
 * Date: 2019-02-09
 * Time: 12:07
 */

namespace Tests\Setup;


use App\Delegate;
use App\Event;
use App\Session;
use Faker\Factory;
use Illuminate\Support\Facades\DB;

class SessionFactory
{

    private $faker;

    /**
     * @var int
     */
    private $moderatorType;

    /**
     * @var int
     */
    private $order;

    /**
     * @var Event
     */
    private $event;

    /**
     * EventFactory constructor.
     */
    public function __construct() {
        $this->faker = Factory::create();
    }


    /**
     * @return Event
     */
    public function create(): Session {

        $event = $this->event ?? \factory(Event::class)->create();

        $data = [
            'event_id' => $event->id,
        ];

        if ($this->order) {
            $data['order'] = $this->order;
        }

        if ($this->moderatorType) {
            $data['moderation_type'] = $this->moderatorType;
        }

        $session = factory(Session::class)->create($data);

        if ($this->moderatorType) {
            DB::table('moderators')->insert([
                'delegate_id' => \factory(Delegate::class)->create([
                    'event_id' => $event->id
                ])->id,
                'session_id'  => $session->id,
            ]);
        }


        return $session;
    }

    /**
     * @param int $moderatorType
     * @return SessionFactory
     */
    public function setModeratorType(int $moderatorType): SessionFactory {
        $this->moderatorType = $moderatorType;

        return $this;
    }

    /**
     * @param Event $event
     * @return SessionFactory
     */
    public function setEvent(Event $event): SessionFactory {
        $this->event = $event;

        return $this;
    }

    /**
     * @param int $order
     * @return SessionFactory
     */
    public function setOrder(int $order): SessionFactory {
        $this->order = $order;

        return $this;
    }

}