<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'name'           => 'Commissioner Gordon',
                'email'          => 'gordon@gothampd.com',
                'password'       => '$2y$10$Y.jEitizf.DW3V7gxCnMr.SdWN2i1w4gobo28vTLGaFajqcjUl8Oy',
                'remember_token' => null,
                'created_at'     => '2021-04-26 12:08:28',
                'updated_at'     => '2021-04-26 12:08:28',
            ]
        ];

        User::insert($users);
    }
}
