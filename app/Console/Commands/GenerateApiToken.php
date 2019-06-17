<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class GenerateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stl:keygen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new API Key specific for every user.';

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
        $users = User::get();
        foreach ($users as $user) {
            $user->api_token = str_random(60);
            $user->save();
        }
    }
}
