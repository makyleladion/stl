<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Transaction;
use App\Bet;
use App\Ticket;
use App\OfflineSyncLog;
use App\System\Services\OfflineSyncService;

class TransactionSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stl:transaction-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize offline transactions, bets, and tickets to main server. Usage: stl:transaction-sync <your_token> <STL link>/api. Example: php artisan stl:transaction-sync 123..60 https://testing.systme.stl.phss/api';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $token = env('OFFLINE_API_TOKEN', "");
        $url = env('OFFLINE_API_URL', "");
        
        $service = new OfflineSyncService();
        $service->transactionSync($token, $url);
    }
}
