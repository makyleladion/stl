<?php

namespace App\Jobs;

use App\MobileNumber;
use App\System\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSmsCustomers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $drawDate;
    
    protected $schedKey;
    
    protected $service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($drawDate, $schedKey)
    {
        $this->drawDate = $drawDate;
        $this->schedKey = $schedKey;
        
        $mn = MobileNumber::where('role', \App\System\Data\MobileNumber::ROLE_CUSTOMER)->get();
        $numbers = [];
        foreach ($mn as $m) {
            $numbers[] = $m->mobile_number;
        }
        
        $this->service = new SmsService($numbers);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->service->sendCustomerResults($this->drawDate, $this->schedKey);
    }
}
