<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stl:clear-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove data completely from database except for user credentials.';

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
        $isOffline = boolval(env('IS_OFFLINE', false));
        if (!$isOffline) {
            echo "Clearing the database is only done in offline mode.\n";
            return;
        }
        
        $exceptTables = [
            'default_outlets',
            'outlets',
            'users',
        ];
        
        try {
            $tables = DB::select('SHOW TABLES');
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            foreach ($tables as $table) {
                $prop = 'Tables_in_'.env('DB_DATABASE');
                $tblName = $table->$prop;
                if (!in_array($tblName, $exceptTables)) {
                    DB::statement(sprintf("TRUNCATE TABLE %s", $tblName));
                }
            }
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            echo "Successfully truncated tables\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
            echo "\n";
        }
    }
}
