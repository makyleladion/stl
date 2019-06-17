<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MobileNumber extends Model
{
    protected $table = 'mobile_numbers';
    
    protected $fillable = [
        'user_id', 'mobile_number', 'do_not_send', 'role',
    ];
    
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
