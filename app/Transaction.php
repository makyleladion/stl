<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'outlet_id', 'user_id', 'transaction_code', 'customer_name', 'origin'
    ];

    public function tickets()
    {
        return $this->hasMany('App\Ticket', 'transaction_id');
    }

    public function bets()
    {
        return $this->hasMany('App\Bet','transaction_id');
    }

    public function winningResult()
    {
        return $this->hasMany('App\WinningResult', 'transaction_id');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Outlet','outlet_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
