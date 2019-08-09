<?php

namespace Tests\Unit;

use App\Delegate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncPositionGroupingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function new_delegate_position() {
        $delegate = factory(Delegate::class)->create();

        $this->assertDatabaseHas('position_groupings', [
            'event_id' => $delegate->event_id,
            'position' => $delegate->position,
            'grouping' => null
        ]);

    }

    /**
     * @test
     */
    public function update_delegate_position() {

        $delegate = factory(Delegate::class)->create();
        $oldPosition = $delegate->position;
        $newPosition = "New Position";

        $delegate->update(['position' => $newPosition]);

        $this->assertDatabaseHas('position_groupings', [
            'event_id' => $delegate->event_id,
            'position' => $newPosition,
            'grouping' => null
        ]);

        $this->assertDatabaseMissing('position_groupings', [
            'event_id' => $delegate->event_id,
            'position' => $oldPosition,
        ]);
    }

    /**
     * @test
     */
    public function delete_delegate() {

        $delegate = factory(Delegate::class)->create();
        $oldPosition = $delegate->position;

        $delegate->delete();

        $this->assertDatabaseMissing('position_groupings', [
            'event_id' => $delegate->event_id,
            'position' => $oldPosition,
        ]);
    }

}
