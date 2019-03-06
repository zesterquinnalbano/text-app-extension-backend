<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'firstname' => 'Daryll',
                'lastname' => 'Daley',
                'username' => 'daryll',
                'password' => Hash::make('daryll123'),
            ],
            [
                'firstname' => 'Gareth',
                'lastname' => 'Callaway',
                'username' => 'gareth',
                'password' => Hash::make('gareth123'),
            ],
        ]);

    }
}
