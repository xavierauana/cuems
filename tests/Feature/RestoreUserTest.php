<?php

namespace Tests\Feature;

use App\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestoreUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function restore_deleted_user() {

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create([
            'is_ldap_user' => true
        ]);

        $admin = factory(User::class)->create();

        $this->actingAs($admin)
             ->delete(route('users.destroy', $user))
             ->assertRedirect(route('users.index'));

        $url = route('users.restore', $user);

        $this->put($url)
             ->assertRedirect(route('users.index'))
             ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'deleted_at' => null,
        ]);
    }
}
