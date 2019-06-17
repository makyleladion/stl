<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketCancellation extends Model
{
    protected $table = 'ticket_cancellations';
    
    protected $fillable = [
        'ticket_id',
        'user_id',
    ];
    
    public function ticket()
    {
        return $this->belongsTo('App\Ticket','ticket_id');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
