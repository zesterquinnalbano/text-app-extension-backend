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
        TwilioNumber::insert([
            [
                'contact_number' => '+16475034763',
                'country' => 'CA',
                'user_id' => 1,
            ],
            [
                'contact_number' => '+16476979613',
                'country' => 'CA',
                'user_id' => 1,
            ],
        ]);
    }
}
