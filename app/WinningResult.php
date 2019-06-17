<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WinningResult extends Model
{
    protected $table = 'winning_results';

    protected $fillable = [
        'user_id', 'game', 'number', 'result_date', 'schedule_key',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public function winningTickets()
    {
        return $this->hasMany('App\WinningTicket','winning_result_id');
    }
}
