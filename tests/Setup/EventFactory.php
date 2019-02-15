<?php
/**
 * Author: Xavier Au
 * Date: 2019-02-09
 * Time: 12:07
 */

namespace Tests\Setup;


use App\Delegate;
use App\DelegateRole;
use App\Event;
use App\Sponsor;
use App\Ticket;
use App\Transaction;
use Faker\Factory;

class EventFactory
{
    /**
     * @var int
     */
    private $delegateCount = 0;

    /**
     * @var \App\DelegateRole
     */
    private $role;

    /**
     * @var \App\Ticket
     */
    private $ticket;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $tickets;

    /**
     * @var int
     */
    private $ticketCount;

    /**
     * @var int
     */
    private $sponsorCount;

    /**
     * @var array
     */
    private $sponsoredDelegateCount = [];


    private $faker;

    /**
     * EventFactory constructor.
     */
    public function __construct() {
        $this->faker = Factory::create();
    }


    /**
     * @return \App\Event
     */
    public function create(): Event {

        $event = factory(Event::class)->create();

        if ($this->delegateCount) {
            $delegates = factory(Delegate::class,
                $this->delegateCount)->create([
                'event_id' => $event->id,
            ]);
            if ($this->role) {
                $delegates->each(function (Delegate $delegate) {
                    $delegate->roles()->save($this->role);
                });
            }
        }

        if ($this->ticketCount) {
            $this->tickets = factory(Ticket::class,
                $this->ticketCount)->create([
                'event_id' => $event->id
            ]);
        }

        if ($this->sponsorCount) {
            factory(Sponsor::class, $this->sponsorCount)->create([
                'event_id' => $event->id
            ]);
        }

        foreach ($this->sponsoredDelegateCount as $sponsorIndex => $numOfDelegate) {
            factory(Delegate::class,
                $numOfDelegate)->create([
                'event_id' => $event->id,
            ])->each(function (Delegate $delegate) use ($sponsorIndex) {
                $delegate->sponsorRecord()->create([
                    'sponsor_id' => optional($delegate->event->sponsors[$sponsorIndex])->id,
                    'tel'        => $this->faker->phoneNumber,
                    'name'       => $this->faker->name,
                    'email'      => $this->faker->companyEmail,
                    'address'    => $this->faker->address,
                ]);
            })->each(function (Delegate $delegate) use ($sponsorIndex) {
                factory(Transaction::class)->create([
                    'payee_type' => Delegate::class,
                    'payee_id'   => $delegate->id
                ]);
            });

        }


        return $event;
    }

    /**
     * @param int $delegateCount
     * @return EventFactory
     */
    public function setDelegateCount(int $delegateCount): EventFactory {
        $this->delegateCount = $delegateCount;

        return $this;
    }

    /**
     * @param mixed $role
     * @return EventFactory
     */
    public function setRole(DelegateRole $role) {
        $this->role = $role;

        return $this;
    }

    /**
     * @param \App\Ticket $ticket
     * @return EventFactory
     */
    public function setTicket(\App\Ticket $ticket): EventFactory {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * @param int $ticketCount
     * @return EventFactory
     */
    public function setTicketCount(int $ticketCount): EventFactory {
        $this->ticketCount = $ticketCount;

        return $this;
    }

    /**
     * @param int $sponsorCount
     * @return EventFactory
     */
    public function setSponsorCount(int $sponsorCount): EventFactory {
        $this->sponsorCount = $sponsorCount;

        return $this;
    }

    /**
     * @param array $sponsoredDelegateCount
     * @param array $sponsorsIndex
     * @return EventFactory
     */
    public function setSponsoredDelegateCount(
        array $sponsoredDelegateCount, array $sponsorsIndex
    ): EventFactory {

        $this->sponsoredDelegateCount = array_combine($sponsorsIndex,
            $sponsoredDelegateCount
        );

        return $this;
    }
}