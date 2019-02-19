<?php

use App\Conversation;
use Illuminate\Database\Seeder;

class ConversationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Conversation::create([
            'twilio_number_id' => 1,
            'contact_id' => 1,
        ]);
    }
}
