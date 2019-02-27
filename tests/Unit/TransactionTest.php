<?php

namespace Tests\Unit;

use App\Transaction;
use App\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function has_transaction_type() {

        $transaction = factory(Transaction::class)->create();

        $this->assertInstanceOf(TransactionType::class,
            $transaction->transactionType);

    }

    /**
     * @test
     */
    public function check_in(){

        


    }
}
