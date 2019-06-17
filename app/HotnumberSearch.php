<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HotnumberSearch extends Model
{
    protected $table = 'hotnumber_searches';
    
    protected $fillable = [
        'user_id', 'result_date', 'schedule_key', 'keyword',
    ];
    
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
