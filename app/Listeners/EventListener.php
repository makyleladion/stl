<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\CalculatedSalesDataEvent;

class EventListener
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
     * @param  CalculatedSalesDataEvent  $event
     * @return void
     */
    public function handle(CalculatedSalesDataEvent $event)
    {
        //
    }
}
