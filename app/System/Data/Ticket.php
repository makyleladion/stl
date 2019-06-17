<?php

namespace App\System\Data;

use App\Outlet;
use App\System\Services\PayoutService;
use App\System\Utils\TimeslotUtils;
use App\Ticket as TicketModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Ticket
{
    private $outlet;
    private $ticket;
    private $amount;

    /**
     * Ticket constructor.
     *
     * @param Outlet $outlet
     * @param TicketModel $ticket
     */
    public function __construct(Outlet $outlet, TicketModel $ticket)
    {
        $this->outlet = $outlet;
        $this->ticket = $ticket;
    }

    /**
     * Draw datetime.
     *
     * Get the draw date and time of the ticket.
     * This will also denote the validity of the ticket.
     *
     * @return string
     */
    public function drawDateTime()
    {
        $date = $this->ticket->result_date;
        $time = Timeslot::getTimeByKey($this->ticket->schedule_key);

        return $date . ' ' . $time;
    }

    /**
     * Draw datetime using Carbon Object.
     *
     * @return static
     */
    public function drawDateTimeCarbon()
    {
        $datetime = $this->drawDateTime();
        return Carbon::parse($datetime);
    }

    /**
     * Ticket number.
     *
     * @return mixed
     */
    public function ticketNumber()
    {
        return $this->ticket->ticket_number;
    }

    /**
     * Bet datetime.
     *
     * Get the date and time the ticket was issued.
     *
     * @return mixed
     */
    public function betDateTime()
    {
        return $this->ticket->created_at;
    }

    public function isCancellableViaApi() {
        try {
            $isPrivileged = \auth()->user()->is_usher || \auth()->user()->is_admin;
            $isActive = \auth()->user()->status == "active";
            if ($isActive) {
                $now = Carbon::now(env('APP_TIMEZONE'));
                $timeLimit = $this->betDateTime()->addMinutes(5);
                return !$now->gt($timeLimit);
            }
        } catch (\Exception $e) {

        }
        
        return false;
    }

    /**
     * Ticket amount.
     *
     * The sum of all amount in peso per bet under a ticket.
     *
     * @return float
     */
    public function amount()
    {
        if (is_null($this->amount)) {
            $this->amount = (float) $this->ticket->bets()->sum('amount');
        }

        return $this->amount;
    }

    /**
     * Get related bet data objects.
     *
     * @return array
     */
    public function bets()
    {
        $bets = [];
        $results = (isset($this->ticket->bets)) ? $this->ticket->bets : $this->ticket->bets()->get();
        foreach ($results as $result) {
            $bets[] = new Bet($this->outlet, $result);
        }

        return $bets;
    }

    /**
     * Check if draw time has already passed.
     *
     * @return bool
     */
    public function isDrawTimePassed($subMins = null)
    {
        return TimeslotUtils::isDrawTimePassed($this->drawDateTime(), null, $subMins);
    }

    /**
     * Get winning bets of a particular ticket.
     *
     * @return array
     */
    public function getWinningBets()
    {
        try {
            $service = new PayoutService();
            $winnings = $service->getTicketWinningBets($this->ticketNumber(), null, false);

            $bets = [];
            foreach ($winnings as $win) {
                $bets[] = $win['obj'];
            }

            return $bets;
        } catch (ModelNotFoundException $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * CHeck if ticket is already paid out.
     *
     * @return bool
     */
    public function hasPaidOut()
    {
        $hasPaidOut = false;
        if ($this->ticket->payout()->count() > 0) {
            $hasPaidOut = true;
        }

        return $hasPaidOut;
    }
    
    /**
     * Check if ticket is canceled.
     * 
     * @return boolean
     */
    public function isCanceled()
    {
        return (bool) $this->ticket->is_cancelled;
    }
    
    public function cancellationDetails()
    {
        if (isset($this->ticket->ticketCancellation)) {
            return $this->ticket->ticketCancellation;
        }
        
        return $this->ticket->ticketCancellation()->first();
    }

    /**
     * Get ticket model instance.
     *
     * @return TicketModel
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Get outlet model instance.
     *
     * @return Outlet
     */
    public function getOutlet()
    {
        return $this->outlet;
    }

    public function getBetGameLabels() {
        $gameLabels = [];
        $bets = $this->bets();
        foreach ($bets as $bet) {
            if (!in_array($bet->gameLabel(), $gameLabels)) {
                $gameLabels[] = $bet->gameLabel();
            }
        }
        return $gameLabels;
    }
}
