<?php

namespace App\System\Data;

use App\Payout as PayoutModel;

class Payout
{
    private $payout;

    private $user;
    private $transaction;
    private $ticket;
    private $bet;
    private $betData;
    private $ticketData;
    private $outlet;

    public function __construct(PayoutModel $payout)
    {
        $this->payout = $payout;
    }

    /**
     * ID.
     * 
     * @return integer
     */
    public function id()
    {
        return $this->payout->id;
    }

    /**
     * Teller.
     * 
     * @return string
     */
    public function teller()
    {
        $this->loadUser();
        return $this->user->name;
    }

    /**
     * Ticket Number of Payout.
     * 
     * @return string
     */
    public function ticketNumber()
    {
        $this->loadTicket();
        return $this->ticket->ticket_number;
    }

    /**
     * Customer Name.
     * 
     * @return string
     */
    public function customerName()
    {
        $this->loadTransaction();
        return $this->transaction->customer_name;
    }

    /**
     * Bet object.
     * 
     * @return \App\System\Data\Bet
     */
    public function bet()
    {
        $this->loadBet();
        $this->loadOutlet();
        if (empty($this->betData)) {
            $this->betData = new Bet($this->outlet, $this->bet);
        }
        return $this->betData;
    }

    /**
     * Draw datetime.
     * 
     * @return \App\System\Data\Ticket
     */
    public function drawDateTime()
    {
        $this->loadOutlet();
        $this->loadTicket();
        if (empty($this->ticketData)) {
            $this->ticketData = new Ticket($this->outlet, $this->ticket);
        }
        return $this->ticketData->drawDateTimeCarbon();
    }

    /**
     * Payout datetime.
     * 
     * @return unknown
     */
    public function payoutDateTime()
    {
        return $this->payout->created_at;
    }

    private function loadUser()
    {
        if (empty($this->user)) {
            if (isset($this->payout->user)) {
                $this->user = $this->payout->user;
            } else {
                $this->user = $this->payout->user()->first();
            }
        }
    }

    private function loadTicket()
    {
        if (empty($this->ticket)) {
            if (isset($this->payout->ticket)) {
                $this->ticket = $this->payout->ticket;
            } else {
                $this->ticket = $this->payout->ticket()->first();
            }
        }
    }

    private function loadBet()
    {
        if (empty($this->bet)) {
            if (isset($this->payout->bet)) {
                $this->bet = $this->payout->bet;
            } else {
                $this->bet = $this->payout->bet()->first();
            }
        }
    }

    private function loadOutlet()
    {
        if (empty($this->outlet)) {
            if (isset($this->payout->outlet)) {
                $this->outlet = $this->payout->outlet;
            } else {
                $this->outlet = $this->payout->outlet()->first();
            }
        }
    }

    private function loadTransaction()
    {
        if (empty($this->transaction)) {
            if (isset($this->payout->transaction)) {
                $this->transaction = $this->payout->transaction;
            } else {
                $this->transaction = $this->payout->transaction()->first();
            }
        }
    }
}
