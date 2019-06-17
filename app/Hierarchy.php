<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hierarchy extends Model
{
    protected $table = 'hierarchy';
    
    protected $fillable = [
        'user_superior_id',
        'user_subordinate_id',
        'relationship_label',
    ];
    
    public function superiorUser()
    {
        return $this->belongsTo('App\User', 'user_superior_id');
    }
    
    public function subordinateUser()
    {
        return $this->belongsTo('App\User', 'user_subordinate_id');
    }
}
