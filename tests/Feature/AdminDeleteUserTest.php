<?php

namespace Tests\Feature;

use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminDeleteUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function delete_user() {

        Carbon::setTestNow(Carbon::create(2019, 1, 1));

        $user = factory(User::class)->create([
            'is_ldap_user' => true
        ]);

        $transaction = factory(Transaction::class)->create();

        DB::table('check_in')->insert([
            'user_id'        => $user->id,
            'transaction_id' => $transaction->id,
            'created_at'     => Carbon::now()->addDays(-1)
        ]);


        $admin = factory(User::class)->create();

        $this->actingAs($admin)
             ->delete(route('users.destroy', $user))
             ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'deleted_at' => Carbon::now()
        ]);
    }
}
