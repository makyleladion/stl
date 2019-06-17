<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;
use App\User;

class LoginNotification extends Mailable
{
    use Queueable, SerializesModels;
    
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $date = Carbon::now(env('APP_TIMEZONE'));
        $subject = sprintf("New Login: %s (%s)", $this->user->name, $date->toDayDateTimeString());
        
        $loginInfo = $this->user->userTimeLogs()->orderBy('log_time', 'desc')->firstOrFail();
        return $this->subject($subject)
            ->view('mail.login')
            ->with([
                'login_info' => $loginInfo,
                'title' => 'Login Notification',
            ]);
    }
}
