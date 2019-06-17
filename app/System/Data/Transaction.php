<?php

namespace App\System\Data;

use App\Outlet;
use App\Transaction as TransactionModel;
use Illuminate\Database\Eloquent\Collection;
use App\System\Utils\GeneratorUtils;
use App\System\Games\GamesFactory;

class Transaction
{
    private $outlet;
    private $transaction;

    private $teller;
    private $numberOfBets;
    private $betsString;
    private $amount;
    private $tickets;
    
    const TRANSACTION_ORIGIN_OUTLET = 'outlet';
    const TRANSACTION_ORIGIN_MOBILE = 'id_mobile_usher';

    /**
     * Transaction constructor.
     *
     * @param Outlet $outlet
     * @param TransactionModel $transaction
     */
    public function __construct(Outlet $outlet, TransactionModel $transaction)
    {
        $this->outlet = $outlet;
        $this->transaction = $transaction;
    }

    /**
     * Transaction id.
     *
     * Get the id of the transaction.
     * Option to pad the id with trailing zeros is available.
     * Padded id is 8 digits long.
     *
     * Example: 00000001
     *
     * @param bool $padded
     * @return mixed|string
     */
    public function id($padded = false)
    {
        if ($padded) {
            return str_pad($this->transaction->id, 8, '0', STR_PAD_LEFT);
        }
        return $this->transaction->id;
    }

    /**
     * Transaction number.
     *
     * @return mixed
     */
    public function transactionNumber()
    {
        return $this->transaction->transaction_code;
    }

    /**
     * Outlet name where transaction is made.
     *
     * @return mixed
     */
    public function outletName()
    {
        return $this->outlet->name;
    }

    /**
     * Customer name with placeholder option.
     *
     * @param string $placeholder
     * @return mixed|string
     */
    public function customerName($placeholder = '')
    {
        if (strlen($placeholder > 0) && strlen($this->transaction->customer_name) <= 0) {
            return $placeholder;
        }
        return $this->transaction->customer_name;
    }

    /**
     * Number of bets under a transaction.
     *
     * @return int
     */
    public function numberOfBets()
    {
        if (is_null($this->numberOfBets)) {
            $this->numberOfBets = $this->transaction->bets()->count();
        }

        return $this->numberOfBets;
    }

    /**
     * Bet string.
     *
     * Concatenate all bet numbers into one string for display.
     *
     * @return string
     */
    public function betsString()
    {
        if (is_null($this->betsString)) {
            if (isset($this->transaction->bets)) {
                $this->betsString = $this->generateBetString($this->transaction->bets);
            } else {
                $this->betsString = $this->generateBetString($this->transaction->bets()->get());
            }
        }
        return $this->betsString;
    }

    /**
     * Total amount made in the transaction.
     *
     * @return float
     */
    public function amount($includeCancelled = false)
    {
        if (is_null($this->amount)) {
            $this->amount = (float) $this->transaction->bets()->whereHas('ticket', function($ticket) use ($includeCancelled) {
                $ticket->where('is_cancelled', $includeCancelled);
            })->sum('amount');
        }

        return $this->amount;
    }

    /**
     * Teller name.
     *
     * The teller that made the transaction.
     *
     * @return mixed
     */
    public function teller()
    {
        return $this->tellerObj()->name;
    }
    
    /**
     * Teller object.
     * 
     * The User object denoting a teller.
     * 
     * @return mixed
     */
    public function tellerObj()
    {
        if (is_null($this->teller)) {
            if (isset($this->transaction->user)) {
                $user = $this->transaction->user;
            } else {
                $user = $this->transaction->user()->first();
            }
            $this->teller = $user;
        }
        
        return $this->teller;
    }

    /**
     * Get related tickets data objects.
     *
     * @return array
     */
    public function tickets($toString = false)
    {
        if (is_null($this->tickets)) {
            $this->tickets = [];
            if (isset($this->transaction->tickets)) {
                $results = $this->transaction->tickets;
            } else{
                $results = $this->transaction->tickets()->get();
            }
            foreach ($results as $result) {
                $this->tickets[] = new Ticket($this->outlet, $result);
            }
        }
        
        if ($toString) {
            $strs = [];
            foreach ($this->tickets as $ticket) {
                $strs[] = $ticket->ticketNumber();
            }
            return GeneratorUtils::generateStringWithDelimiter($strs, '<br>');
        }

        return $this->tickets;
    }
    
    public function canceledBy()
    {
        $tickets = $this->tickets();
        $strs = [];
        foreach ($tickets as $ticket) {
            $cancel = $ticket->cancellationDetails();
            if ($cancel) {
                $user = $cancel->user()->first();
                $strs[] = $user->name;
            }
        }
        
        return GeneratorUtils::generateStringWithDelimiter($strs, ', ');
    }
    
    public function cancelDates()
    {
        $tickets = $this->tickets();
        $strs = [];
        foreach ($tickets as $ticket) {
            $cancel = $ticket->cancellationDetails();
            if ($cancel) {
                $strs[] = $cancel->created_at->toDayDateTimeString();
            }
        }
        
        return GeneratorUtils::generateStringWithDelimiter($strs, ', ');
    }

    /**
     * Transaction datetime.
     *
     * @return mixed
     */
    public function transactionDateTime()
    {
        return $this->transaction->created_at;
    }

    /**
     * Get transaction model instance.
     *
     * @return TransactionModel
     */
    public function getTransaction()
    {
        return $this->transaction;
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

    /**
     * Algorithm for generating bet string.
     *
     * @param Collection $bets
     * @return string
     */
    private function generateBetString(Collection $bets)
    {
        $betsStr = '';
        $i = 0;
        foreach ($bets as $bet) {
            $game = GamesFactory::getGame($bet->game);
            $abvr = $game->betTypeAbbreviation($bet->type);
            $betsStr .= sprintf("%s - %s - PHP %d", $bet->number, $abvr, $bet->amount);
            if(++$i < count($bets)) {
                $betsStr .= '<br />';
            }
        }
        return $betsStr;
    }

    public function getBetGameLabels($toString =  false) {
        $gameLabels = [];
        $gameLabelsStr = "";
        $tickets = $this->tickets();
        foreach ($tickets as $ticket) {
            $gameLabels = array_unique(array_merge($ticket->getBetGameLabels(),$gameLabels));
        }
        $i=0;
        if ($toString) {
            foreach ($gameLabels as $label) {
                 $gameLabelsStr .= $label;
                if(++$i < count($gameLabels)) {
                    $gameLabelsStr .= ', ';
                }
            }
            return $gameLabelsStr;
        }

        return $gameLabels;
    }
    
    public static function getTransactionOrigins()
    {
        return [
            self::TRANSACTION_ORIGIN_MOBILE => 'mobile',
            self::TRANSACTION_ORIGIN_OUTLET => 'outlet',
        ];
    }
    
    public function getDrawDateTimes()
    {
        $tickets = $this->tickets();
        $drawDateTimes = [];
        foreach ($tickets as $ticket) {
            $drawDateTime = $ticket->drawDateTimeCarbon()->toDayDateTimeString();
            if (!in_array($drawDateTime, $drawDateTimes)) {
                $drawDateTimes[] = $drawDateTime;
            }
        }
        
        return GeneratorUtils::generateStringWithDelimiter($drawDateTimes, ', ');
    }
}
