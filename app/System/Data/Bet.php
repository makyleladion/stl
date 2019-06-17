<?php

namespace App\System\Data;

use App\Bet as BetModel;
use App\Outlet;
use App\System\Game;
use App\System\Games\GamesFactory;
use App\System\Games\Pares\ParesGame;
use App\System\Games\Swertres\SwertresGame;
use App\System\Games\SwertresSTL\SwertresSTLGame;
use App\System\Games\Swertres\SwertresService;
use App\System\Games\SwertresSTLLocal\SwertresSTLLocalGame;

class Bet
{
    private $outlet;
    private $bet;
    private $game;

    /**
     * Bet constructor.
     *
     * @param Outlet $outlet
     * @param BetModel $bet
     */
    public function __construct(Outlet $outlet, BetModel $bet)
    {
        $this->outlet = $outlet;
        $this->bet = $bet;
        $this->game = GamesFactory::getGame($this->bet->game, $outlet);
    }

    /**
     * Bet number.
     *
     * @param boolean $permute
     * @return mixed
     */
    public function betNumber($permute = false)
    {
        if ($permute && ($this->bet->game === SwertresGame::name() || $this->bet->game === SwertresSTLGame::name() || $this->bet->game === SwertresSTLLocalGame::name()) && $this->betType() === SwertresGame::TYPE_RAMBLED) {
            $service = new SwertresService();
            $output = $service->permutation($this->bet->number);
            $html = '';
            foreach ($output as $out) {
                $html .= "<span class=\"bet-perm-bet-num\">" . implode($out) . "</span>, ";
            }
            return $html;
        }
        return $this->bet->number;
    }

    /**
     * Bet type.
     *
     * @return mixed
     * @throws \Exception
     */
    public function betType($excludeNone = false)
    {
        if (in_array($this->bet->type, $this->game->betTypes())) {
            if ($excludeNone && $this->bet->type == ParesGame::TYPE_NONE) {
                return '';
            }
            return $this->bet->type;
        }
        throw new \Exception('BetType ' . $this->bet->type . ' does not exist.');
    }

    public function betTypeAbbreviation()
    {
        return $this->game->betTypeAbbreviation($this->bet->type);
    }

    /**
     * Bet amount.
     *
     * Amount in peso for a particular bet.
     *
     * @return float
     */
    public function amount()
    {
        return (float) $this->bet->amount;
    }

    /**
     * Bet price.
     *
     * The possible price with the given bet.
     *
     * @return mixed
     */
    public function price()
    {
        return $this->game->price(
            $this->bet->number,
            $this->amount(),
            $this->betType(),
            $this->bet->created_at->toDateString()
        );
    }

    /**
     * Game label.
     *
     * Display name of the game played for a particular bet.
     *
     * @return mixed
     */
    public function gameLabel()
    {
        return $this->game->label();
    }

    /**
     * Get bet model instance.
     *
     * @return BetModel
     */
    public function getBet()
    {
        return $this->bet;
    }
    
    public function getTicket()
    {
        if ($this->bet->ticket && $this->bet->outlet) {
            return new Ticket($this->bet->outlet, $this->bet->ticket);
        }
        
        return new Ticket(
            $this->bet->outlet()->first(),
            $this->bet->ticket()->first()
        );
    }
}
