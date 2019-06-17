<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\User;

class CalculatedSalesDataEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $user;
    
    public $amount;
    
    public $ticket_count;
    
    public $bet_schedule;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $amount, $ticket_count, $bet_schedule)
    {
        $this->user = $user;
        $this->amount = (float) $amount;
        $this->ticket_count = (int) $ticket_count;
        $this->bet_schedule = $bet_schedule;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('sales-broadcast');
    }
    
    public function broadcastAs()
    {
        return 'sales-broadcast.calculated';
    }
}
