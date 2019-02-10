<?php

namespace Tests\Unit;

use App\Delegate;
use App\Sponsor;
use App\SponsorRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SponsorRecordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function delegate() {

        $record = factory(SponsorRecord::class)->create();

        $this->assertInstanceOf(Delegate::class, $record->delegate);

    }

    /**
     * @test
     */
    public function sponsor() {
        $record = factory(SponsorRecord::class)->create();

        $this->assertInstanceOf(Sponsor::class, $record->sponsor);

    }
}
