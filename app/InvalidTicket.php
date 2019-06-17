<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvalidTicket extends Model
{
    protected $table = 'invalid_tickets';

    protected $fillable = [
        'user_id',
        'transaction_code',
        'outlet_id',
        'ticket_number',
        'result_date',
        'schedule_key',
        'amount',
        'error',
        'source'
    ];

    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

    public function outlet() {
        return $this->belongsTo('App\Outlet','outlet_id');
    }

}
