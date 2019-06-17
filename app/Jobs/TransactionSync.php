<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\System\Services\OfflineSyncService;

class TransactionSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $isOffline =  env('IS_OFFLINE', false);
        $token = env('OFFLINE_API_TOKEN');
        $url = env('OFFLINE_API_URL');
        
        if ($isOffline && (!empty($token) && !empty($url))) {
            $service = new OfflineSyncService();
            $service->transactionSync($token, $url);
        }
    }
}
