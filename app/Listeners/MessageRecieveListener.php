<?php

namespace App\Listeners;

use App\Events\MessageRecieveEvent;
use Log;

// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Contracts\Queue\ShouldQueue;

class MessageRecieveListener
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MessageRecieveEvent  $event
     * @return void
     */
    public function handle(MessageRecieveEvent $event)
    {
        Log::info($event);
    }
}
