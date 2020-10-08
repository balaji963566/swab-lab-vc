<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'name'           => 'Admin',
                'email'          => 'admin@admin.com',
                'password'       => '$2y$10$l0Bty/Caswo5ePVUq8htdekgbS9wlh2nHzz6URg3EloH.30tH1RSO',
                'remember_token' => null,
            ],
        ];

        User::insert($users);
    }
}