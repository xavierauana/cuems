<?php

namespace Tests\Unit;

use App\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class TransactionTypeTest extends TestCase
{
    use RefreshDatabase;


    /**
     * @test
     */
    public function has_many_transactions() {

        $transactionType = factory(TransactionType::class)->create();

        $this->assertInstanceOf(Collection::class,
            $transactionType->transactions);
    }

}
