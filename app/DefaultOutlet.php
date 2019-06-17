<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefaultOutlet extends Model
{
    protected $table = 'default_outlets';

    protected $fillable = [
        'user_id', 'outlet_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Outlet', 'outlet_id');
    }
}
