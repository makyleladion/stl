<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\TransactionSocketEvent;

class TransactionSocketEventListener
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
     * @param  TransactionSocketEvent  $event
     * @return void
     */
    public function handle(TransactionSocketEvent $event)
    {
        //
    }
}
