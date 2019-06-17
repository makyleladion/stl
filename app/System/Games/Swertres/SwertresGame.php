<?php

namespace App\System\Games\Swertres;


use App\Outlet;
use App\System\Game;
use App\System\Data\Price;

class SwertresGame implements Game
{
    const TYPE_STRAIGHT  = 'straight';
    const TYPE_RAMBLED   = 'rambled';
    
    const GAME_LABEL = "Swertres Lotto";

    protected $outlet;
    protected $pricePerPeso;
    protected $service;
    protected $price;

    public function __construct(Outlet $outlet = null)
    {
        $this->outlet = $outlet;
        $this->service = new SwertresService();
        $this->price = new Price();
    }

    /**
     * Name of the game.
     *
     * @return string
     */
    public static function name()
    {
        return 'swertres';
    }

    /**
     * Game label to display in UI.
     *
     * @return string
     */
    public function label()
    {
        return self::GAME_LABEL;
    }

    /**
     * Bet Types.
     *
     * A bet has types of betting. List them all in an array.
     *
     * @return array
     */
    public function betTypes()
    {
        return [
            self::TYPE_STRAIGHT,
            self::TYPE_RAMBLED,
        ];
    }

    /**
     * Determine the price of bet.
     *
     * @param $bet
     * @param $amount
     * @param $type
     * @return float
     * @throws \Exception
     */
    public function price($bet, $amount, $type, $drawDate = null)
    {
        if (!$this->isValidBet($bet)) {
            throw new \Exception('Please enter a valid bet number.');
        }

        if (!is_numeric($amount)) {
            throw new \Exception('Amount must be an integer or float.');
        }
        
        if (!$drawDate) {
            $drawDate = date('Y-m-d');
        }

        // Get price per outlet
        if (is_null($this->pricePerPeso)) {
            $this->pricePerPeso = $this->price->getPrice($this, $drawDate);
        }
        
        $divisor = ($this->service->countPermutation($bet) > 3) ? $this->service->countPermutation($bet) : 3;

        if (self::TYPE_STRAIGHT == $type) {
            $price = $this->pricePerPeso * $amount;
        } else if (self::TYPE_RAMBLED == $type) {
            $price = ($this->pricePerPeso * $amount) / $divisor;
        } else {
            throw new \Exception('Invalid Swertres Bet type: ' . $type);
        }

        return $price;
    }

    /**
     * Bet Validity.
     *
     * Test whether a bet entry or number is valid on a particular game.
     *
     * @param $bet
     * @return boolean
     */
    public function isValidBet($bet)
    {
        return $this->service->isValidBet($bet);
    }

    /**
     * Bet Type Abbreviation.
     *
     * @param $type
     * @return string
     */
    public function betTypeAbbreviation($type)
    {
        if ($type === self::TYPE_STRAIGHT) {
            return 'S';
        } else if ($type === self::TYPE_RAMBLED) {
            return 'R';
        }

        return ''; 
    }

    /**
     * Algorithm to check if a bet is win.
     *
     * @param $winningNumber
     * @param $toCompareBetNumber
     * @param $type
     * @return mixed
     */
    public function isWin($winningNumber, $toCompareBetNumber, $type)
    {
        if ($this->isValidBet($winningNumber) && $this->isValidBet($toCompareBetNumber)) {
            if ($type === self::TYPE_STRAIGHT && ($winningNumber == $toCompareBetNumber)) {
                return true;
            } else if ($type === self::TYPE_RAMBLED) {
                foreach ($this->service->permutation($toCompareBetNumber) as $cmpBet) {
                    if ($winningNumber == implode($cmpBet)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
    
    public function isSoldOutRule($currentAmount, $toCheck, $type)
    {
        return ($currentAmount + $toCheck) > 1000;
    }
}
