<?php

namespace App\System;

interface Game
{
    /**
     * Name of the game.
     *
     * @return string
     */
    public static function name();

    /**
     * Game label to display in UI.
     *
     * @return string
     */
    public function label();

    /**
     * Bet Types.
     *
     * A bet has types of betting. List them all in an array.
     *
     * @return array
     */
    public function betTypes();

    /**
     * Bet Validity.
     *
     * Test whether a bet entry or number is valid on a particular game.
     *
     * @param $bet
     * @return mixed
     */
    public function isValidBet($bet);

    /**
     * Determine the price of bet.
     *
     * @param $bet
     * @param $amount
     * @param $type
     * @param $drawDate
     * @return float
     */
    public function price($bet, $amount, $type, $drawDate = null);

    /**
     * Bet Type Abbreviation.
     *
     * @param $type
     * @return string
     */
    public function betTypeAbbreviation($type);

    /**
     * Algorithm to check if a bet is win.
     *
     * @param $winningNumber
     * @param $toCompareBetNumber
     * @param $type
     * @return mixed
     */
    public function isWin($winningNumber, $toCompareBetNumber, $type);
    
    /**
     * Sold Out Rule.
     * 
     * @param $currentAmount
     * @param $toCheck
     */
    public function isSoldOutRule($currentAmount, $toCheck, $type);
}
