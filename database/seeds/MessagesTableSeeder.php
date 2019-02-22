<?php

use App\Message;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Message::insert([
            [
                'conversation_id' => 1,
                'direction' => 'INBOUND',
                'message' => 'hi',
                'sent_by' => null,
                'status' => 'recieved',
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}
