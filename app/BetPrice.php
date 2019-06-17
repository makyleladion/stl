<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BetPrice extends Model
{
    protected $table = 'bet_prices';

    protected $fillable = [
        'outlet_id', 'type', 'price_per_bet_count'
    ];
}
