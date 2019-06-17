<?php

namespace App\System\Data;

use App\Bet;
use App\System\Games\GamesFactory;
use Carbon\Carbon;
use App\System\Utils\TimeslotUtils;

class Winner
{
    protected $bet;
    protected $game;
    protected $drawDate;
    
    public function __construct(Bet $bet, $drawDate = null)
    {
        $this->bet = $bet;
        $this->game = GamesFactory::getGame($this->bet->game, $this->bet->outlet()->first());
        if (TimeslotUtils::validateDate($drawDate)) {
            $this->drawDate = $drawDate;
        } else {
            $this->drawDate = date('Y-m-d');
        }
    }
    
    public function ticketNumber()
    {
        if (isset($this->bet->ticket)) {
            return $this->bet->ticket->ticket_number;
        }
        return $this->bet->ticket()->first()->ticket_number;
    }
    
    public function outletName()
    {
        if (isset($this->bet->ticket->outlet)) {
            return $this->bet->ticket->outlet->name;
        }
        return $this->bet->outlet()->first()->name;
    }

    public function outletId()
    {
        if (isset($this->bet->ticket->outlet)) {
            return $this->bet->ticket->outlet->id;
        }
        return $this->bet->outlet()->first()->id;
    }
    
    public function teller()
    {
        if (isset($this->bet->ticket->user)) {
            return $this->bet->ticket->user->name;
        }
        return $this->bet->ticket()->first()->user()->first()->name;
    }
    
    public function customer()
    {
        if (isset($this->bet->transaction)) {
            return $this->bet->transaction->customer_name;
        }
        return $this->bet->transaction()->first()->customer_name;
    }
    
    public function bet()
    {
        return str_replace(':', '-', $this->bet->number);
    }
    
    public function game()
    {
        return $this->game->label();
    }
    
    public function gameName()
    {
        return $this->game::name();
    }
    
    public function type()
    {
        return $this->bet->type;
    }
    
    public function amount()
    {
        return $this->bet->amount;
    }
    
    public function winningPrize()
    {
        return $this->game->price($this->bet->number, $this->bet->amount, $this->bet->type, $this->drawDate);
    }
    
    public function drawDateTime()
    {
        $date = $this->bet->ticket()->first()->result_date;
        $time = Timeslot::getTimeByKey($this->bet->ticket()->first()->schedule_key);
        
        return $date . ' ' . $time;
    }
    
    public function drawDateTimeCarbon()
    {
        return Carbon::parse($this->drawDateTime());
    }
}
