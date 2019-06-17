<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WinningTicket extends Model
{
    protected $table = 'winning_tickets';
    
    protected $fillable = [
        'winning_result_id', 'ticket_id', 'bet_id',
    ];
    
    public function winningResult()
    {
        return $this->belongsTo('App\WinningResult','winning_result_id');
    }
    
    public function ticket()
    {
        return $this->belongsTo('App\Ticket', 'ticket_id');
    }
    
    public function bet()
    {
        return $this->belongsTo('App\Bet','bet_id');
    }
}
