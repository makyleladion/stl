<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $fillable = [
        'user_id', 'user_creator_id', 'name', 'address', 'status', 'is_affiliated'
    ];

    public function transactions()
    {
        return $this->hasMany('App\Transaction', 'outlet_id');
    }

    public function tickets()
    {
        return $this->hasMany('App\Ticket', 'outlet_id');
    }

    public function bets()
    {
        return $this->hasMany('App\Bet','outlet_id');
    }

    public function betPrices()
    {
        return $this->hasMany('App\BetPrice', 'outlet_id');
    }

    public function winningResult()
    {
        return $this->hasMany('App\WinningResult', 'outlet_id');
    }
    
    public function payouts()
    {
        return $this->hasMany('App\Payout','outlet_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
    
    public function tellers()
    {
        return $this->hasMany('App\DefaultOutlet','outlet_id');
    }
}
