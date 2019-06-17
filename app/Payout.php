<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'user_id',
        'outlet_id',
        'transaction_id',
        'ticket_id',
        'bet_id',
        'winning_result_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Outlet','outlet_id');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Transaction','transaction_id');
    }

    public function ticket()
    {
        return $this->belongsTo('App\Ticket','ticket_id');
    }

    public function bet()
    {
        return $this->belongsTo('App\Bet','bet_id');
    }

    public function winningResult()
    {
        return $this->belongsTo('App\WinningResult','winning_result_id');
    }
}
