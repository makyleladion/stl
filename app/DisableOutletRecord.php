<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DisableOutletRecord extends Model
{
    protected $table = 'disable_outlet_records';
    
    protected $fillable = [
        'outlet_id', 'disable_timestamp',
    ];
    
    public $timestamps = false;
}
