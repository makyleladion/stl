<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'message', 'datetime'
    ];


    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

}
