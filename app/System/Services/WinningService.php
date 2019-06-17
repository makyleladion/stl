<?php

namespace App\System\Services;

use App\Ticket;
use App\WinningTicket;
use App\System\Data\Winner;
use App\System\Games\GamesFactory;
use App\Outlet;
use App\Bet;
use Illuminate\Support\Facades\DB;
use App\System\Game;
use App\System\Utils\PaginationUtils;
use App\System\Games\Swertres\SwertresGame;
use App\System\Data\Timeslot;
use App\System\Data\User;

class WinningService
{
    private $cache;
    
    public function __construct()
    {
        $this->cache = new CachingService();
    }
    
    public function getTicketsWithWinningBets($winningBet, $game, $drawDate, $schedKey)
    {
        $tickets = Ticket::where([
            'result_date' => $drawDate,
            'schedule_key' => $schedKey,
            'is_cancelled' => false,
        ])->get();
        
        $winningTicketsAndBets = [];
        
        foreach ($tickets as $ticket) {
            $tmp = [];
            $outlet = $ticket->outlet()->first();          
            foreach ($ticket->bets()->get() as $bet) {
                $gameObj = GamesFactory::getGame($game, $outlet);                
                if ($gameObj->isWin($winningBet, $bet->number, $bet->type)) {
                    $tmp[] = $bet;
                }
            }
            
            if (count($tmp) > 0) {
                $winningTicketsAndBets[] = [
                    'ticket' => $ticket,
                    'bets' => $tmp,
                ];
            }
        }
        
        return $winningTicketsAndBets;
    }
    
    public function getWinningBetsStored($drawDate, $drawDateTo = null, $fromApi = false)
    {
        $query = WinningTicket::with('bet','ticket','ticket.outlet','ticket.user','bet.transaction');
        
        if (!auth()->user()->is_superadmin) {
            $allowedId = [];
            if (!$fromApi) {
                foreach ($this->cache->getUserLeaves(auth()->user()) as $sub) {
                    $allowedId[] = $sub->id;
                }
                $allowedIds[] = auth()->user()->id;
            } else {
                $allowedId[] = auth()->user()->id;
            }
            $query->whereHas('ticket.user', function($user) use ($allowedId) {
                $user->whereIn('id', $allowedId);
            });
        }
        
        $query->join('winning_results','winning_tickets.winning_result_id','=','winning_results.id');
        
        if (is_null($drawDateTo)) {
            $query->whereDate('winning_results.result_date',$drawDate);
            $winners = $query->get();
        } else {
            $query->whereBetween('winning_results.result_date', [$drawDate, $drawDateTo]);
            $winners = $query->get();
        }
        
        $winnersData = [];
        foreach ($winners as $winner) {
            $winnersData[] = new Winner($winner->bet, $drawDate);
        }
        
        return $winnersData;
    }

    public function winnersAggregrateByDrawDateTime(array $winners)
    {
        $service = new CachingService();
        
        $collection = [];
        foreach ($winners as $winner) {
            if ($winner instanceof Winner) {
                if (!in_array($winner->drawDateTime(), array_keys($collection))) {
                    $collection[$winner->drawDateTime()] = [
                        'winners' => [],
                        'total_amount' => 0,
                    ];
                }

                $collection[$winner->drawDateTime()]['winners'][] = $winner;
                $collection[$winner->drawDateTime()]['total_amount'] += $winner->winningPrize();
            }
        }

        return $collection;
    }
    
    public function winnersAggregrateByGames(array $winners)
    {      
        $collection = [];
        foreach ($winners as $winner) {
            if ($winner instanceof Winner) {
                if (!in_array($winner->gameName(), array_keys($collection))) {
                    $collection[$winner->gameName()] = [
                        'winners' => [],
                        'total_amount' => 0,
                    ];
                }
                
                $collection[$winner->gameName()]['winners'][] = $winner;
                $collection[$winner->gameName()]['total_amount'] += $winner->winningPrize();
            }
        }
        
        return $collection;
    }
    
    public function totalWinningAmountAllocation($drawDate)
    {
        $winners = $this->getWinningBetsStored($drawDate);
        
        $amount = 0;
        foreach ($winners as $winner) {
            $amount += $winner->winningPrize();
        }
        
        return $amount;
    }
    
    public function countWinnings($drawDate, $schedKey, Outlet $outlet = null, $user_id = 0)
    {
        if ($outlet) {
            return WinningTicket::whereHas('winningResult', function($q) use ($drawDate, $schedKey) {
                $q->where([
                    'result_date' => $drawDate,
                    'schedule_key' => $schedKey,
                ]);
            })->whereHas('ticket', function($q) use ($outlet, $user_id) {
                $q->where('outlet_id', $outlet->id);
                if ($user_id > 0 && is_numeric($user_id)) {
                    $q->where('user_id', $user_id);
                }
            })->count();
        }
        
        return WinningTicket::whereHas('winningResult', function($q) use ($drawDate, $schedKey) {
            $q->where([
                'result_date' => $drawDate,
                'schedule_key' => $schedKey,
            ]);
        })->count();
    }
    
    public function getHighestWinningBets($drawDate, $schedKey = null, $amount = 100, $winning = 50000, $isWinObj = true)
    {
        if ($schedKey) {
            $bets = Bet::with('outlet')
            ->with('ticket')
            ->with('transaction')
            ->whereHas('ticket', function($q) use ($drawDate, $schedKey) {
                $q->where([
                    'result_date' => $drawDate,
                    'schedule_key' => $schedKey,
                ]);
            })->where('amount', '>=', $amount)->get();
        } else {
            $bets = Bet::with('outlet')
            ->with('ticket')
            ->with('transaction')
            ->whereHas('ticket', function($q) use ($drawDate, $schedKey) {
                $q->where([
                    'result_date' => $drawDate,
                ]);
            })->where('amount', '>=', $amount)->get();
        }
        
        $topBets = [];
        foreach ($bets as $bet) {
            if (!$isWinObj) {
                $betObj = new \App\System\Data\Bet($bet->outlet, $bet);
                if ($betObj->price() >= $winning) {
                    $topBets[] = $betObj;
                }
            } else {
                $winObj = new Winner($bet);
                if ($winObj->winningPrize() >= $winning) {
                    $topBets[] = $winObj;
                }
            }
        }
        
        return $topBets;
    }
    
    public function getHotNumbers($drawDate, $skip, $take, $game = null, $schedKey = null, $hotnumber = 3000)
    {
        if (empty($game)) {
            $game = SwertresGame::name();
        }
        
        $where = [];
        $where['bets.game'] = $game;
        $where['result_date'] = $drawDate;
        if ($schedKey) {
            $where['schedule_key'] = $schedKey;
        }
        
        $query = Bet::query();
        $query->select(DB::raw(
                'number,COUNT(*) as bet_count,SUM(amount) as total_amount,(CASE WHEN (SUM(amount) >= ?) THEN true ELSE false END) as is_hotnumber'
            ));
        $query->join('tickets','bets.ticket_id','=','tickets.id');
        $query->where($where);
        $query->groupBy('bets.number');
        $query->orderBy('total_amount','desc');
        $query->skip($skip);
        $query->take($take);
        $query->addBinding($hotnumber, 'select');
        $bets = $query->get();
        
        return $bets;
    }
}
