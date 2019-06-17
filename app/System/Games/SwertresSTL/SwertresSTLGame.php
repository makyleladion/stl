<?php
namespace App\System\Games\SwertresSTL;

use App\Outlet;
use App\System\Games\Swertres\SwertresGame;

class SwertresSTLGame extends SwertresGame
{
    const GAME_LABEL = "Swer3 STL";
    
    public function __construct(Outlet $outlet = null)
    {
        parent::__construct($outlet);
    }
    
    /**
     * Name of the game.
     *
     * @return string
     */
    public static function name()
    {
        return 'swertres_stl';
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
     * Bet Type Abbreviation.
     *
     * @param $type
     * @return string
     */
    public function betTypeAbbreviation($type)
    {
        if ($type === self::TYPE_STRAIGHT) {
            return 'S (STL)';
        } else if ($type === self::TYPE_RAMBLED) {
            return 'R (STL)';
        }
        
        return '';
    }
    
    public function isSoldOutRule($currentAmount, $toCheck, $type)
    {
        if ($type == parent::TYPE_STRAIGHT) {
            return ($currentAmount + $toCheck) > 500;
        }
        
        return false;
    }
}
