<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $users = [
            [
                'name'     => "Xavier Au",
                'email'    => "xavier.au@anacreation.com",
                'password' => "aukaiyuen",
            ]
        ];

        foreach ($users as $user) {
            $new_user = new User();
            $new_user->name = $user['name'];
            $new_user->email = $user['email'];
            $new_user->password = bcrypt($user['password']);
            $new_user->email_verified_at = \Carbon\Carbon::now();
            $new_user->save();
        }
    }
}
