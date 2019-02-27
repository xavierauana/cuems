<?php

namespace Tests\Feature;

use App\Delegate;
use App\DelegateRole;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckInTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function sent_token_to_get_user_data() {

        $this->actingAs(factory(\App\User::class)->create());
        factory(Delegate::class)->create();
        $delegate = factory(Delegate::class)->create();

        factory(Transaction::class)->create([
            'payee_type' => get_class($delegate),
            'payee_id'   => $delegate->id,
        ]);

        $token = $delegate->transactions->first()->uuid;
        $uri = route('checkin.getDelegate', $token);
        $response = $this->json("POST", $uri);

        $expectedJsonData = [
            'first_name' => $delegate->first_name,
            'last_name'  => $delegate->first_name,
            'ticket'     => ($transaction = $delegate->transactions->first()) ? $transaction->ticket->name : null,
            'role'       => $delegate->roles->map(function (DelegateRole $role
            ) {
                return $role->label;
            })->reduce(function ($carry, $roleLabel) {
                return $carry .= $roleLabel . ", ";
            }, ""),
            'check_in'   => null,
        ];
        $response->assertJson([
            "data" => $expectedJsonData
        ]);

    }

    /**
     * @test
     */
    public function check_in_user() {
        $this->actingAs(factory(\App\User::class)->create());
        factory(Delegate::class)->create();
        $delegate = factory(Delegate::class)->create();

        factory(Transaction::class)->create([
            'payee_type' => get_class($delegate),
            'payee_id'   => $delegate->id,
        ]);

        $knownDate = Carbon::create(2001, 5, 21, 12);
        Carbon::setTestNow($knownDate);

        $token = $delegate->transactions->first()->uuid;
        $uri = route('checkin.delegate', $token);
        $this->json("POST", $uri)
                         ->assertJson(['status' => 'completed']);

    }
    /**
     * @test
     */
    public function check_in_user_verify() {
        $this->actingAs(factory(\App\User::class)->create());
        factory(Delegate::class)->create();
        $delegate = factory(Delegate::class)->create();

        factory(Transaction::class)->create([
            'payee_type' => get_class($delegate),
            'payee_id'   => $delegate->id,
        ]);

        $knownDate = Carbon::create(2001, 5, 21, 12);
        Carbon::setTestNow($knownDate);

        $token = $delegate->transactions->first()->uuid;
        $uri = route('checkin.delegate', $token);
        $this->json("POST", $uri);

        $token = $delegate->transactions->first()->uuid;
        $uri = route('checkin.getDelegate', $token);
        $response = $this->json("POST", $uri);

        $expectedJsonData = [
            'first_name' => $delegate->first_name,
            'last_name'  => $delegate->first_name,
            'ticket'     => ($transaction = $delegate->transactions->first()) ? $transaction->ticket->name : null,
            'role'       => $delegate->roles->map(function (DelegateRole $role
            ) {
                return $role->label;
            })->reduce(function ($carry, $roleLabel) {
                return $carry .= $roleLabel . ", ";
            }, ""),
            'check_in'   => Carbon::now()->toDateTimeString(),
        ];
        $response->assertJson([
            "data" => $expectedJsonData
        ]);

    }


}
