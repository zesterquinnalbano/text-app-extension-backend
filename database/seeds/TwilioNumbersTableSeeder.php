<?php

use App\TwilioNumber;
use Illuminate\Database\Seeder;

class TwilioNumbersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TwilioNumber::create([
            'contact_number' => '+16475034763',
            'country' => 'CA',
            'user_id' => 1,
        ]);
    }
}
