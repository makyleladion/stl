<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\System\Data\Outlet;
use \App\System\Data\User as UserData;
use Illuminate\Support\Facades\Cache;
use App\System\Utils\ConfigUtils;

class EnableOutlets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stl:enable-outlets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable outlets';

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
        if (ConfigUtils::get('ENABLE_BETTING')) {
            DB::table('outlets')->where('status', Outlet::STATUS_DISABLED)->update(['status' => Outlet::STATUS_ACTIVE]);
            DB::table('users')->where('is_betting_enabled', UserData::BETTING_INACTIVE)->update(['is_betting_enabled' => UserData::BETTING_ACTIVE]);
            Cache::flush();
        }
    }
}
