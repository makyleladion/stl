<?php
namespace App\System\Data;

use App\System\Game;
use App\System\Games\Pares\ParesGame;
use App\System\Games\Swertres\SwertresGame;
use Carbon\Carbon;
use App\System\Games\SwertresSTL\SwertresSTLGame;
use App\System\Games\SwertresSTLLocal\SwertresSTLLocalGame;

class Price
{
    private $priceMap;
    
    public function __construct()
    {
        $this->createPriceMap();
    }
    
    public function getPrice(Game $game, $drawDate)
    {
        $pricesCmp = [];
        $date = Carbon::createFromFormat('Y-m-d', $drawDate, env('APP_TIMEZONE'));
        foreach ($this->priceMap as $pDate => $price) {
            $priceDate = Carbon::createFromFormat('Y-m-d', $pDate, env('APP_TIMEZONE'));
            $index = $priceDate->diffInDays($date, false);
            if ($index >= 0) {
                $pricesCmp[$index] = $price;
            }
        }
        $priceKey = min(array_keys($pricesCmp));
        return $pricesCmp[$priceKey][$game::name()];
    }

    private function createPriceMap()
    {
        $this->priceMap = [
            '2018-10-12' => [
                SwertresGame::name() => 450,
                SwertresSTLGame::name() => 450,
                ParesGame::name() => 0,
                SwertresSTLLocalGame::name() => 450,
            ],
            '2018-07-02' => [
                SwertresGame::name() => 500,
                SwertresSTLGame::name() => 500,
                ParesGame::name() => 0,
                SwertresSTLLocalGame::name() => 500,
            ],
            '2018-01-22' => [
                SwertresGame::name() => 500,
                SwertresSTLGame::name() => 500,
                ParesGame::name() => 800,
                SwertresSTLLocalGame::name() => 0,
            ],
            '2017-12-03' => [
                SwertresGame::name() => 450,
                SwertresSTLGame::name() => 450,
                ParesGame::name() => 800,
                SwertresSTLLocalGame::name() => 0,
            ],
            '2017-12-02' => [
                SwertresGame::name() => 500,
                SwertresSTLGame::name() => 500,
                ParesGame::name() => 800,
                SwertresSTLLocalGame::name() => 0,
            ],
            '2017-11-25' => [
                SwertresGame::name() => 450,
                SwertresSTLGame::name() => 450,
                ParesGame::name() => 800,
                SwertresSTLLocalGame::name() => 0,
            ],
        ];
    }
}
