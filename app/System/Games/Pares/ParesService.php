<?php

namespace App\System\Games\Pares;

class ParesService
{
    public function isValidBet($bet)
    {
        if (substr_count($bet, ':') == 1) {
            list($firstDraw, $secondDraw) = explode(':', $bet);
            if ((is_numeric($firstDraw) && is_numeric($secondDraw))
                && (($firstDraw >= 1 && $firstDraw <= 40)
                && ($secondDraw >= 1 && $secondDraw <= 40))) {
                return true;
            }
        }
        
        return false;
    }
    
    public function removeLeadingZeros($bet)
    {
        if ($this->isValidBet($bet)) {
            list($left, $right) = explode(':', $bet);
            $left = ltrim($left, '0');
            $right = ltrim($right, '0');
            
            return $left.':'.$right;
        }
        
        throw new \Exception('Error removing leading zeros with Pares bet.');
    }
    
    public function addLeadingZero($bet)
    {
        if ($this->isValidBet($bet)) {
            list($left, $right) = explode(':', $bet);
            $left = str_pad($left, 2, '0', STR_PAD_LEFT);
            $right = str_pad($right, 2, '0', STR_PAD_LEFT);
            
            return $left.':'.$right;
        }
        
        throw new \Exception('Error adding leading zero with Pares bet.');
    }
}
