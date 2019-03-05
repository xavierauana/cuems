<?php

namespace Tests\Unit;

use App\Transaction;
use App\TransactionType;
use App\User;
use Carbon\Carbon;
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
    public function check_in() {
        $user = factory(User::class)->create();
        $knownDate = Carbon::create(2001, 5, 21, 12);
        Carbon::setTestNow($knownDate);

        $transaction = factory(Transaction::class)->create();
        $transaction->checkIn($user);

        $this->assertDatabaseHas('check_in', [
            'transaction_id' => $transaction->id,
            'created_at'     => $knownDate->toDateTimeString()
        ]);

    }

    /**
     * @test
     */
    public function get_check_in_records() {
        $user = factory(User::class)->create();
        $transaction = factory(Transaction::class)->create();

        $knownDate1 = Carbon::create(2001, 5, 21, 12);
        Carbon::setTestNow($knownDate1);
        $transaction->checkIn($user);

        $knownDate2 = Carbon::create(2001, 5, 21, 13);
        Carbon::setTestNow($knownDate2);
        $transaction->checkIn($user);

        $records = $transaction->getCheckInRecords();

        $this->assertEquals([
            [
                'timestamp' => $knownDate2->toDateTimeString(),
                'user'      => $user->refresh()
            ],
            [
                'timestamp' => $knownDate1->toDateTimeString(),
                'user'      => $user->refresh()
            ],
        ], $records);

    }
}
