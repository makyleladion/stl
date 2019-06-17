<?php

namespace App\System\Games\Pares;

use App\Outlet;
use App\System\Game;
use App\System\Data\Price;

class ParesGame implements Game
{
    const TYPE_NONE = 'none';
    const GAME_LABEL = 'Pares';

    protected $outlet;
    protected $pricePerPeso;
    protected $price;
    
    private $service;

    public function __construct(Outlet $outlet = null)
    {
        $this->outlet = $outlet;
        $this->service = new ParesService();
        $this->price = new Price();
    }

    /**
     * Name of the game.
     *
     * @return string
     */
    public static function name()
    {
        return 'pares';
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
            self::TYPE_NONE,
        ];
    }

    /**
     * Bet Validity.
     *
     * Test whether a bet entry or number is valid on a particular game.
     *
     * @param $bet
     * @return mixed
     */
    public function isValidBet($bet)
    {
        return $this->service->isValidBet($bet);
    }

    /**
     * Determine the price of bet.
     *
     * @param $bet
     * @param $amount
     * @param $type
     * @return float
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

        if (self::TYPE_NONE == $type) {
            $price = $this->pricePerPeso * $amount;
        } else {
            throw new \Exception('Invalid Pares Bet type: ' . $type);
        }

        return $price;
    }

    /**
     * Bet Type Abbreviation.
     *
     * @param $type
     * @return string
     */
    public function betTypeAbbreviation($type)
    {
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
            $winningNumber = $this->service->removeLeadingZeros($winningNumber);
            $toCompareBetNumber = $this->service->removeLeadingZeros($toCompareBetNumber);
            return $winningNumber == $toCompareBetNumber;
        }

        return false;
    }
    
    public function isSoldOutRule($currentAmount, $toCheck, $type)
    {
        return false;
    }
}
