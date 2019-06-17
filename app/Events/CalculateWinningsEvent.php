<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\System\Services\WinningService;
use App\System\Games\GamesFactory;
use Carbon\Carbon;

class CalculateWinningsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $success;
    
    public $message;
    
    public $total_per_game;
    
    public $total;
    
    public $date_range_text;
    
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($dateFrom, $dateTo)
    {
        $dateFromCarbon = Carbon::createFromFormat('Y-m-d', $dateFrom, env('APP_TIMEZONE'));
        $dateToCarbon = Carbon::createFromFormat('Y-m-d', $dateTo, env('APP_TIMEZONE'));
        
        $this->date_range_text = sprintf("%s to %s",
                $dateFromCarbon->toFormattedDateString(),
                $dateToCarbon->toFormattedDateString()
            );
        
        try {
            $service = new WinningService();
            $winners = $service->getWinningBetsStored($dateFrom, $dateTo);
            $winnersAggregated = $service->winnersAggregrateByGames($winners);
            $this->total = $this->calculateTotal($winners);
            foreach (GamesFactory::getGameNames() as $gameName) {
                if (!array_key_exists($gameName, $winnersAggregated)) {
                    $this->total_per_game[$gameName] = 0;
                } else {
                    $this->total_per_game[$gameName] = $winnersAggregated[$gameName]['total_amount'];
                }
            }
            
            $this->success = true;
            $this->message = '';
        } catch (\Exception $e) {
            report($e);
            $this->success = false;
            $this->message = $e->getMessage();
        }
    }
    
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('winnings-broadcast');
    }
    
    public function broadcastAs()
    {
        return 'winnings-broadcast.calculated';
    }
    
    private function calculateTotal(array $winners)
    {
        $amount = 0;
        foreach ($winners as $winner) {
            $amount += $winner->winningPrize();
        }
        
        return $amount;
    }
}
