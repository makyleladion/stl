<?php
/**
 * Created by PhpStorm.
 * User: joshu
 * Date: 25/10/2017
 * Time: 10:14 AM
 */

namespace App\System\Utils;


use App\Ticket;
use App\Transaction;

class GeneratorUtils
{
    /**
     * Transaction code.
     *
     * @return string
     */
    public static function transactionCode()
    {
        $code = self::genericCode();
        while (Transaction::where('transaction_code', $code)->count() > 0) {
            $code = self::genericCode();
        }
        return $code;
    }

    /**
     * Ticket number.
     *
     * @return string
     */
    public static function ticketNumber()
    {
        $code = self::genericCode();
        while (Ticket::where('ticket_number', $code)->count() > 0) {
            $code = self::genericCode();
        }
        return $code;
    }

    /**
     * Generic code algorithm.
     *
     * @return string
     */
    public static function genericCode()
    {
        return sprintf("%s-%s-%s-%s",
            self::randomPaddedDigit(),
            self::randomPaddedDigit(),
            self::randomPaddedDigit(),
            self::randomPaddedDigit()
        );
    }

    /**
     * Random padded digit.
     *
     * @return string
     */
    public static function randomPaddedDigit()
    {
        $allowedCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $padStr = '';
        $max = strlen($allowedCharacters) - 1;
        for ($i = 0; $i < 4; $i++) {
            $padStr .= $allowedCharacters[mt_rand(0, $max)];
        }
        return $padStr;
    }

    /**
     * Generate string with delimiter.
     *
     * @param array $strs
     * @param $delimiter
     * @return string
     */
    public static function generateStringWithDelimiter(array $strs, $delimiter)
    {
        $str = '';
        $i = 0;
        foreach ($strs as $s) {
            $str .= $s;
            if(++$i < count($strs)) {
                $str .= $delimiter;
            }
        }
        return $str;
    }
}
