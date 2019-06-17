<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\System\Utils\DbUtils;

class BackupDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stl:backupdb {destination?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database into a file.';

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
        // Craete the backup
        $destination = $this->argument('destination');
        $filename = sprintf("stl-%s.sql", date('Y-m-d'));
        DbUtils::backupDBAsFile($filename, $destination);
    }
}
