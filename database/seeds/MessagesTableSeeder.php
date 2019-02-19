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
                'status' => null,
                'created_at' => Carbon::now(),
            ],
            [
                'conversation_id' => 1,
                'direction' => 'OUTBOUND',
                'message' => 'hello',
                'sent_by' => 1,
                'status' => 'sent',
                'created_at' => Carbon::now(),
            ],
            [
                'conversation_id' => 1,
                'direction' => 'INBOUND',
                'message' => 'how are you',
                'sent_by' => null,
                'status' => null,
                'created_at' => Carbon::now(),
            ],
            [
                'conversation_id' => 1,
                'direction' => 'OUTBOUND',
                'message' => 'im fine',
                'sent_by' => 1,
                'status' => 'sent',
                'created_at' => Carbon::now(),
            ],
            [
                'conversation_id' => 1,
                'direction' => 'OUTBOUND',
                'message' => 'you',
                'sent_by' => 1,
                'status' => 'sent',
                'created_at' => Carbon::now(),
            ],
            [
                'conversation_id' => 1,
                'direction' => 'INBOUND',
                'message' => 'im fine too',
                'sent_by' => null,
                'status' => null,
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}
