<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTimeLog extends Model
{
    public $timestamps = false;
    
    protected $table = 'user_time_logs';
    
    protected $fillable = [
        'user_id', 'log_time', 'mode', 'ip_address', 'agent', 'description',
    ];
    
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
