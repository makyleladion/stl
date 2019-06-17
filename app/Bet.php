<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    protected $fillable = [
        'transaction_id', 'outlet_id', 'ticket_id', 'amount', 'type', 'number', 'game',
    ];

    public function winningResult()
    {
        return $this->hasOne('App\WinningResult', 'bet_id');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Outlet', 'outlet_id');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Transaction', 'transaction_id');
    }

    public function ticket()
    {
        return $this->belongsTo('App\Ticket', 'ticket_id');
    }
}
