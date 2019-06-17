<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'is_admin', 'is_read_only', 'api_token', 'is_usher', 'is_betting_enabled',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function outlets()
    {
        return $this->hasMany('App\Outlet', 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction', 'user_id');
    }

    public function tickets()
    {
        return $this->hasMany('App\Ticket', 'user_id');
    }

    public function defaultOutlet()
    {
        return $this->hasOne('App\DefaultOutlet', 'user_id');
    }

    public function winningResults()
    {
        return $this->hasMany('App\WinningResult', 'user_id');
    }
    
    public function userTimeLogs()
    {
        return $this->hasMany('App\UserTimeLog', 'user_id');
    }
    
    public function mobileNumbers()
    {
        return $this->hasMany('App\MobileNumber', 'user_id');
    }
  
    public function invalidTickets()
    {
        return $this->hasMany('App\InvalidTicket', 'user_id');
    }
    
    public function superiorHierarchy()
    {
        return $this->hasMany('App\Hierarchy', 'user_superior_id');
    }
    
    public function subordinateHierarchy()
    {
        return $this->hasOne('App\Hierarchy', 'user_subordinate_id');
    }
}
