<?php

namespace App\Console\Commands;

use App\System\Utils\ConfigUtils;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TellersAndUshersLoginSummary;

class SendTellersAndUshersLoginSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stl:send-email-logins {user*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send summary of logins from all tellers and ushers.';

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
        try {
            $user = $this->argument('user');
            if (Auth::attempt(['email' => $user[0], 'password' => $user[1]])) {
                $emails = ConfigUtils::get('NOTIFICATION_EMAILS');
                if ($emails) {
                    foreach ($emails as $email) {
                        Mail::to($email)->send(new TellersAndUshersLoginSummary());
                    }
                }
            }
        } catch (\Exception $e) {
            report($e);
            echo $e->getMessage()."\n";
        }
    }
}
