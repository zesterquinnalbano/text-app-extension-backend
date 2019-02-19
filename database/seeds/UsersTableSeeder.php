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
        User::create([
            'firstname' => 'Zester',
            'lastname' => 'Albano',
            'username' => 'admin',
            'password' => Hash::make('admin'),
        ]);
    }
}
