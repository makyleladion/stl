<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfflineSyncLog extends Model
{
    protected $table = 'offline_sync_logs';
    public $timestamps = false;
}
