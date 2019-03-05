<?php

namespace Tests\Feature;

use App\Event;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DelegateFormTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function one_set_sponsor_shown_in_form() {
        $this->withoutExceptionHandling();
        $event1 = factory(Event::class)->create();
        $event2 = factory(Event::class)->create();

        $sponsors1 = [
            "A company",
            "B company",
            "C company",
        ];
        $sponsors2 = [
            "D company",
            "E company",
            "F company",
        ];

        foreach ($sponsors1 as $sponsor) {
            $event1->sponsors()->create([
                'name' => $sponsor
            ]);
        }
        foreach ($sponsors2 as $sponsor) {
            $event2->sponsors()->create([
                'name' => $sponsor
            ]);
        }

        $this->actingAs(factory(User::class)->create());
        $response = $this->get(route('events.delegates.create', $event1));
        foreach ($sponsors1 as $sponsor) {
            $response->assertSee($sponsor);
        }
        foreach ($sponsors2 as $sponsor) {
            $response->assertDontSee($sponsor);
        }
        $response = $this->get(route('events.delegates.create', $event2));
        foreach ($sponsors1 as $sponsor) {
            $response->assertDontSee($sponsor);
        }
        foreach ($sponsors2 as $sponsor) {
            $response->assertSee($sponsor);
        }
    }
}
