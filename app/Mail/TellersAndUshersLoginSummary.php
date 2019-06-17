<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\System\Services\OutletService;
use App\Outlet;
use Carbon\Carbon;

class TellersAndUshersLoginSummary extends Mailable
{
    use Queueable, SerializesModels;
    
    public $outlets;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $service = new OutletService();
        
        $query = Outlet::where('status', '<>', \App\System\Data\Outlet::STATUS_CLOSED);
        $this->outlets = $service->getOutlets(0, 1000);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $date = Carbon::now(env('APP_TIMEZONE'));
        $subject = sprintf("Tellers and Ushers Login Summary as of %s", $date->toDayDateTimeString());
        return $this->subject($subject)
        ->view('mail.tellersandushersloginsummary')->with([
            'title' => 'Tellers and Ushers Daily Login Summary',
        ]);
    }
}
