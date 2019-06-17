<?php

namespace App\System\Services;

use App\Outlet;
use App\Payout;
use App\System\Games\GamesFactory;
use App\Ticket;
use App\Bet as BetModel;
use App\WinningResult;
use App\System\Data\Bet;
use App\System\Data\Ticket as TicketData;
use App\System\Data\Payout as PayoutData;
use App\System\Game;
use App\System\Utils\QueryUtils;
use Illuminate\Support\Facades\DB;
use App\System\Utils\UserUtils;

class PayoutService
{
    private $outlet;

    public function __construct(Outlet $outlet = null)
    {
        $this->outlet = $outlet;
    }

    /**
     * Get winning result.
     *
     * @param $game
     * @param $drawDate
     * @param $scheduleKey
     * @return string
     */
    public function getWinningResult($game, $drawDate, $scheduleKey)
    {
        $win = WinningResult::where([
            'game' => $game,
            'result_date' => $drawDate,
            'schedule_key' => $scheduleKey,
        ])->first();

        if ($win) {
            return $win->number;
        }

        return '';
    }

    /**
     * Get ticket winning bets.
     *
     * @param $ticketNumber
     * @param null $outletId
     * @return array
     */
    public function getTicketWinningBets($ticketNumber, $outletId = null, $strict = true)
    {       
        if (!is_null($outletId)) {
            $outlet = Outlet::findOrFail($outletId);
            $ticket = $outlet->tickets()->where([
                'ticket_number' => trim($ticketNumber),
                'is_cancelled' => false,
            ])->firstOrFail();
        } else {
            $ticket = null;
            if ($ticketNumber instanceof Ticket) {
                $ticket = $ticketNumber;
            } else {
                $ticket = Ticket::where([
                    'ticket_number' => trim($ticketNumber),
                    'is_cancelled' => false,
                ])->firstOrFail();
            }
        }
        $ticketData = new TicketData(($ticket->outlet) ? $ticket->outlet : $ticket->outlet()->first(), $ticket);

        $winningBets = [];
        $letItPass = ($strict) ? $ticketData->isDrawTimePassed() : true;
        if ($letItPass && !$ticketData->hasPaidOut() && !$ticketData->isCanceled()) {
            $winnings = WinningResult::where([
                'result_date' => $ticket->result_date,
                'schedule_key' => $ticket->schedule_key,
            ])->get();

            $bets = $ticket->bets()->get();

            foreach ($bets as $bet) {
                $outletTmp = $bet->outlet()->first();
                foreach ($winnings as $winning) {
                    $game = GamesFactory::getGame($winning->game, $outletTmp);
                    if ($game::name() == $bet->game) {
                        if ($game->isWin($winning->number, $bet->number, $bet->type)) {
                            $winningBets[] = [
                                'obj' => new Bet($outletTmp, $bet),
                                'win' => $winning,
                            ];
                        }
                    }
                }
            }
        } else if ($ticketData->hasPaidOut()) {
            throw new \Exception('The ticket has already been paid out.');
        } else if ($ticketData->isCanceled()) {
            throw new \Exception('You cannot payout a canceled ticket.');
        }

        return $winningBets;
    }

    /**
     * Get Payouts.
     * 
     * @param integer $skip
     * @param integer $take
     * @return \App\System\Data\Payout[]
     */
    public function getPayouts($skip, $take)
    {
        $payouts = [];
        if (empty($this->outlet)) {
            
            $query = Payout::with('user','ticket','bet','outlet','transaction')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->skip($skip)
                ->take($take);
            
            if (!auth()->user()->is_superadmin) {
                $allowedIds = UserUtils::currentAndSubordinatesIds(auth()->user());
                $query->whereHas('user', function($q) use ($allowedIds) {
                    $q->whereIn('id', $allowedIds);
                });
            }
            
            $results = $query->get();
        } else {
            $results = $this->outlet->payouts()
                ->with('user','ticket','bet','outlet','transaction')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->skip($skip)
                ->take($take)
                ->get();
        }

        foreach ($results as $result) {
            $payouts[] = new PayoutData($result);
        }

        return $payouts;
    }
    
    public function getPayoutsPerDate($date, $skip, $take, $paginate = true, $userId = 0)
    {
        $payouts = [];
        if (empty($this->outlet)) {
            $query = Payout::with('user','ticket','bet','outlet','transaction')
            ->whereRaw('date(created_at) = ?', [$date])
            ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');
            if ($paginate) {
                $query = QueryUtils::appendSkipTake($query, $skip, $take);
            }
            $results = $query->get();
        } else {
            $query = $this->outlet->payouts()
            ->with('user','ticket','bet','outlet','transaction')
            ->whereHas('user', function($user) use ($userId) {
                if ($userId > 0) {
                    $user->where('id', $userId);
                }
            })
            ->whereRaw('date(created_at) = ?', [$date])
            ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');
            if ($paginate) {
                $query = QueryUtils::appendSkipTake($query, $skip, $take);
            }
            $results = $query->get();
        }
        
        //dd(DB::getQueryLog());
        
        foreach ($results as $result) {
            $payouts[] = new PayoutData($result);
        }
        
        return $payouts;
    }
    
    /**
     * Get total payouts amount.
     * 
     * @param string $drawDate
     * @param string $schedKey
     * @return number|mixed
     */
    public function getTotalPayoutsAmount($drawDate, $schedKey = null)
    {
        $payouts = [];
        $where = [
            'result_date' => $drawDate,
            'is_cancelled' => false,
        ];
        if (!is_null($schedKey)) {
            $where['schedule_key'] = $schedKey;
        }
        if (empty($this->outlet)) {
            $results = Payout::whereHas('ticket', function($ticket) use ($where) {
                $ticket->where($where);
            })->get();
        } else {
            $results = $this->outlet->payouts()->whereHas('ticket', function($ticket) use ($where) {
                $ticket->where($where);
            })->whereHas('user', function($user) {
                $user->where('is_admin', false);
            })->get();
        }
        
        foreach ($results as $result) {
            $payouts[] = new PayoutData($result);
        }
        
        $totalPayouts = 0;
        foreach ($payouts as $payout) {
            $totalPayouts += $payout->bet()->price();
        }
        
        return $totalPayouts;
    }
    
    public function getTotalPayoutsAmountBySchedule($date, $schedKey = null, $userId = 0)
    {
        $payouts = [];
        $where = [
            'is_cancelled' => false,
        ];
        if (!is_null($schedKey)) {
            $where['schedule_key'] = $schedKey;
        }
        if (empty($this->outlet)) {
            $results = Payout::whereHas('ticket', function($ticket) use ($where) {
                $ticket->where($where);
            })->whereRaw('date(created_at) = ?', [$date])->get();
        } else {
            $results = $this->outlet->payouts()->whereHas('ticket', function($ticket) use ($where) {
                $ticket->where($where);
            })->whereHas('user', function($user) use ($userId) {
                $user->where('is_admin', false);
                if ($userId > 0) {
                    $user->where('id', $userId);
                }
            })->whereRaw('date(created_at) = ?', [$date])->get();
        }
        
        foreach ($results as $result) {
            $payouts[] = new PayoutData($result);
        }
        
        $totalPayouts = 0;
        foreach ($payouts as $payout) {
            $totalPayouts += $payout->bet()->price();
        }
        
        return $totalPayouts;
    }
}
