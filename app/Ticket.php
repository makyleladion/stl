<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'transaction_id',
        'outlet_id',
        'user_id',
        'ticket_number',
        'result_date',
        'schedule_key',
        'print_count',
        'is_cancelled',
    ];

    public function bets()
    {
        return $this->hasMany('App\Bet','ticket_id');
    }

    public function payout()
    {
        return $this->hasOne('App\Payout','ticket_id');
    }
    
    public function winningTickets()
    {
        return $this->hasMany('App\WinningTicket', 'ticket_id');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Transaction', 'transaction_id');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Outlet', 'outlet_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
    
    public function ticketCancellation()
    {
        return $this->hasOne('App\TicketCancellation','ticket_id');
    }
}
