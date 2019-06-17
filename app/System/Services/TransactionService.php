<?php

namespace App\System\Services;

use App\Bet as BetModel;
use App\Outlet;
use App\Ticket as TicketModel;
use App\Transaction as TransactionModel;
use App\System\Data\Bet;
use App\System\Data\Ticket;
use App\System\Data\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\System\Data\Timeslot;
use App\System\Games\GamesFactory;
use App\System\Utils\PaginationUtils;
use Illuminate\Support\Facades\Cache;
use App\System\Games\SwertresSTL\SwertresSTLGame;
use App\System\Data\User;
use App\System\Utils\UserUtils;

class TransactionService
{
    private $outlet;
    
    private $totalAmountArrayCache;
    
    private $origin;

    /**
     * TransactionService constructor.
     *
     * @param Outlet|null $outlet
     */
    public function __construct(Outlet $outlet = null, $origin = 'outlet')
    {
        $this->outlet = $outlet;
        $this->totalAmountArrayCache = [];
        $this->origin = $origin;
    }

    /**
     * Get transactions.
     *
     * Used for paginating transactions in pages.
     * If outlet is provided, only return transactions from that outlet.
     *
     * @param $skip
     * @param $take
     * @return array
     */
    public function getTransactions($skip, $take, TransactionFilter $filter = null, $fromMobile = false)
    {
        
        $transactions = [];
        if (is_null($this->outlet)) {
            $service = new CachingService();
            $model = TransactionModel::with('outlet', 'bets', 'tickets', 'tickets.ticketCancellation')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->skip($skip)
                ->take($take);
            
            if (!auth()->user()->is_superadmin) {
                $allowedIds = UserUtils::leavesIds(auth()->user(), $fromMobile);
                $model->whereHas('user', function ($q) use ($allowedIds) {
                    $q->whereIn('id', $allowedIds);
                });
            }
            
            if ($filter) {
                $model = $filter->execute($model);
            }
            
            $results = $model->paginate($take);
        } else {
            $results = $this->outlet->transactions()
                ->with('outlet', 'bets', 'tickets', 'tickets.ticketCancellation')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->skip($skip)
                ->take($take)
                ->paginate($take);
        }

        foreach ($results as $result) {
            $transactions[] = new Transaction($result->outlet, $result);
        }
        return [ 'transactions' => $transactions, 'raw_transactions' => $results ];
    }

    /**
     * Return a single transaction as array,
     *
     * Used for displaying a single transaction to identify different tickets and bets.
     *
     * @param $transactionId
     * @return array
     * @throws \Exception
     */
    public function getTransactionDataAsArray($transactionId)
    {
        $transaction = null;
        if ($transactionId instanceof Transaction) {
            $transaction = $transactionId;
        } else if ($transactionId instanceof TransactionModel) {
            if (is_null($this->outlet)) {
                $outlet = $transactionId->outlet->first();
                $transaction = new Transaction($outlet, $transactionId);
            } else {
                $transaction = new Transaction($this->outlet, $transactionId);
            }
        } else if (is_numeric($transactionId)) {
            $transaction = new Transaction(
                $this->outlet,
                TransactionModel::findOrFail($transactionId)
            );
        } else {
            throw new \Exception('Invalid supplied argument in the parameter.');
        }

        // Format result
        $transactionArray = [];

        // Retrieve transactions
        $results = BetModel::with('ticket','outlet')
            ->where(['bets.transaction_id' => $transaction->getTransaction()->id])
            ->get();

        foreach ($results as $result) {
            $tmp = [];

            // Transactions
            $tmp['transaction_id'] = $transaction->id(true);
            $tmp['transaction_number'] = $transaction->transactionNumber();
            $tmp['customer_name'] = $transaction->customerName();
            $tmp['amount'] = $transaction->amount();
            $tmp['teller'] = $transaction->teller();
            $tmp['transaction_datetime'] = $transaction->transactionDateTime();

            // Tickets
            $ticket = new Ticket($result->outlet, $result->ticket);
            $tmp['draw_datetime'] = Carbon::parse($ticket->drawDateTime())->toDayDateTimeString();
            $tmp['ticket_id'] = $ticket->getTicket()->id;
            $tmp['ticket_number'] = $ticket->ticketNumber();
            $tmp['bet_datetime'] = $ticket->betDateTime();
            $tmp['draw_datetime_is_passed'] = $ticket->isDrawTimePassed();
            $tmp['is_cancelled'] = $ticket->isCanceled();

            // Bet
            $bet = new Bet($result->outlet, $result);
            $tmp['bet_number'] = $bet->betNumber();
            $tmp['bet_type'] = ucfirst($bet->betType());
            $tmp['bet_amount'] = $bet->amount();
            $tmp['bet_price'] = $bet->price();
            $tmp['bet_game'] = $bet->gameLabel();

            $transactionArray[] = $tmp;
        }

        return $transactionArray;
    }

    /**
     * Get total amount of all transactions.
     *
     * Used to display the amount of all transactions (or by outlet).
     *
     * @param null $carbonObject
     * @return float
     */
    public function getTotalAmountByDate($carbonObject = null)
    {
        if (is_null($carbonObject)) {
            $carbonObject = Carbon::now(env('APP_TIMEZONE'));
        }
        
        $rawData = $this->newGetTotalAmountByDate($carbonObject->format('Y-m-d'));
        $amount = 0;
        foreach ($rawData as $r) {
            $amount = (double) bcadd($amount, $r['amount']);
        }
        
        return $amount;
    }
    
    public function getTotalAmountByDateAndTime($drawDate, $schedKey)
    {
        $carbonUntil = Carbon::createFromFormat('Y-m-d H:i:s',
                       sprintf("%s %s", $drawDate, Timeslot::getTimeByKey($schedKey)),
                       env('APP_TIMEZONE'));
        
        if ($schedKey == Timeslot::TIMESLOT_MORNING) {
            
            $carbonFrom = Carbon::createFromFormat('Y-m-d H:i:s',
                sprintf("%s %s", $drawDate, '00:00:00'),
                env('APP_TIMEZONE'));
            
            return (double) BetModel::whereHas('ticket', function($q) {
                $q->where('is_cancelled', false);
            })->where('created_at', '>=', $carbonFrom->toDateTimeString())
                ->where('created_at', '<=', $carbonUntil->toDateTimeString())
                ->sum('amount');
            
        } else if ($schedKey == Timeslot::TIMESLOT_AFTERNOON) {
            
            $carbonFrom = Carbon::createFromFormat('Y-m-d H:i:s',
                          sprintf("%s %s", $drawDate, Timeslot::getTimeByKey(Timeslot::TIMESLOT_MORNING)),
                          env('APP_TIMEZONE'));
            
            return (double) BetModel::whereHas('ticket', function($q) {
                $q->where('is_cancelled', false);
            })->where('created_at', '>=', $carbonFrom->toDateTimeString())
                ->where('created_at', '<=', $carbonUntil->toDateTimeString())
                ->sum('amount');
            
        } else if ($schedKey == Timeslot::TIMESLOT_EVENING) {
            
            $carbonFrom = Carbon::createFromFormat('Y-m-d H:i:s',
                          sprintf("%s %s", $drawDate, Timeslot::getTimeByKey(Timeslot::TIMESLOT_AFTERNOON)),
                          env('APP_TIMEZONE'));
            
            return (double) BetModel::whereHas('ticket', function($q) {
                $q->where('is_cancelled', false);
            })->where('created_at', '>=', $carbonFrom->toDateTimeString())
                ->where('created_at', '<=', $carbonUntil->endOfDay()->toDateTimeString())
                ->sum('amount');
        }
        
        throw new \Exception('Error in supplying Schedule Key.');
    }

    /**
     * Count daily sales.
     *
     * @param $drawDate
     * @param $schedKey
     * @return float
     */
    public function dailySales($drawDate, $schedKey = null, $useUpdateDateAsCmp = false, $userId = 0)
    {
        $where = [
            'is_cancelled' => false,
        ];
        if (!$useUpdateDateAsCmp) {
            $where['result_date'] = $drawDate;
        }
        if (!is_null($schedKey)) {
            $where['schedule_key'] = $schedKey;
        }
        if (is_null($this->outlet)) {
            $amount = BetModel::whereHas('ticket', function($ticket) use ($where, $drawDate, $useUpdateDateAsCmp) {
                $ticket->where($where);
                if ($useUpdateDateAsCmp) {
                    $ticket->whereRaw('date(created_at) = ?', [$drawDate]);
                }
            })->sum('bets.amount');
            
        } else {
            
            $drawDateCarbon = Carbon::createFromFormat('Y-m-d', $drawDate, env('APP_TIMEZONE'));
            $markDateCarbon = Carbon::createFromFormat('Y-m-d', '2018-07-14', env('APP_TIMEZONE'));
            
            $amountsQuery = BetModel::query();
            $amountsQuery->select(DB::raw('bets.outlet_id,tickets.schedule_key,SUM(bets.amount) as amount'));
            $amountsQuery->join('tickets','bets.ticket_id','=','tickets.id');
            $amountsQuery->join('transactions', 'bets.transaction_id','=','transactions.id');
            $amountsQuery->where('tickets.outlet_id', $this->outlet->id);
            if ($schedKey) {
                $amountsQuery->where('tickets.schedule_key', $schedKey);
            }
            
            if ($drawDateCarbon->gte($markDateCarbon)) {
                $amountsQuery->whereDate('tickets.created_at', $drawDate);
                $amountsQuery->where('tickets.result_date',$drawDate);
            } else if ($drawDateCarbon->eq($markDateCarbon->subDay())) {
                $amountsQuery->where('tickets.result_date',$drawDate);
            } else {
                $amountsQuery->whereDate('tickets.created_at', $drawDate);
            }
            
            $amountsQuery->where('tickets.is_cancelled', false);
            $amountsQuery->where('transactions.origin', $this->origin);

            if (is_numeric($userId) && $userId > 0) {
                $amountsQuery->where('transactions.user_id', $userId);
            }

            $amountsQuery->groupBy('bets.outlet_id');
            $amountsQuery->groupBy('tickets.schedule_key');
            $amountsQuery->getQuery();
            $amounts = $amountsQuery->get();
            
            $amount = 0;
            foreach ($amounts as $m) {
                $amount += $m->amount;
            }
        }
        
        return (float) $amount;
    }
    
    /**
     * Get total number of tickets.
     * 
     * @param string $drawTime
     * @param string $schedKey
     * @return number
     */
    public function getTotalTickets($drawTime, $schedKey = null, $user_id = 0, $isCancelled = false, $getAllForAdmin = false)
    {
        $tickets = $this->getTickets($drawTime, $schedKey, $user_id, $isCancelled, $getAllForAdmin);
        return $tickets->count();
    }

    public function getTickets($drawTime, $schedKey = null, $user_id = 0, $isCancelled = false, $getAllForAdmin = false) {
        $where = [
            'result_date' => $drawTime,
            'is_cancelled' => $isCancelled,
        ];
        if (!is_null($schedKey)) {
            $where['schedule_key'] = $schedKey;
        }

        if (is_numeric($user_id) && $user_id > 0 && !$getAllForAdmin ) {
            $where['user_id'] = $user_id;
        }
        
        if (!is_null($this->outlet)) {
            return $this->outlet->tickets()->where($where);
        }
        
        return TicketModel::where($where);
    }
    
    public function getDailySalesPerOutlet($drawDate, $useOrigin = false, $ushersOnly = false)
    {
        $drawDateCarbon = Carbon::createFromFormat('Y-m-d', $drawDate, env('APP_TIMEZONE'));
        $markDateCarbon = Carbon::createFromFormat('Y-m-d', '2018-07-14', env('APP_TIMEZONE'));
        
        $query = BetModel::query();
        $query->select(DB::raw('bets.outlet_id,tickets.schedule_key,SUM(bets.amount) as amount'));
        $query->join('tickets','bets.ticket_id','=','tickets.id');
        
        if ($useOrigin) {
            $query->join('transactions','bets.transaction_id','=','transactions.id');
            $query->where('transactions.origin', 'like', $this->origin.'%');
            
            if ($ushersOnly) {
                $query->join('users','transactions.user_id','users.id');
                $query->where('users.is_usher', true);
            }
        }
        
        if ($drawDateCarbon->gte($markDateCarbon)) {
            $query->whereDate('tickets.created_at', $drawDate);
            $query->where('tickets.result_date',$drawDate);
        } else if ($drawDateCarbon->eq($markDateCarbon->subDay())) {
            $query->where('tickets.result_date',$drawDate);
        } else {
            $query->whereDate('tickets.created_at', $drawDate);
        }
        
        $query->where('tickets.is_cancelled', false);
        $query->groupBy('bets.outlet_id');
        $query->groupBy('tickets.schedule_key');
        $query->getQuery();
        $amounts = $query->get();
        
        $finalStructure = [];
        foreach ($amounts as $amount) {
            $finalStructure[$amount->outlet_id][$amount->schedule_key] = (float) $amount->amount;
        }
        
        return $finalStructure;
    }
    
    public function getDailySalesPerUser($drawDate, $ushersOnly = false)
    {
        $drawDateCarbon = Carbon::createFromFormat('Y-m-d', $drawDate, env('APP_TIMEZONE'));
        $markDateCarbon = Carbon::createFromFormat('Y-m-d', '2018-07-14', env('APP_TIMEZONE'));
        
        $query = BetModel::query();
        $query->select(DB::raw("users.id as 'user_id',SUM(bets.amount) as 'bet_amount',tickets.schedule_key,users.name as 'teller_name'"));
        $query->join('tickets', 'bets.ticket_id','=','tickets.id');
        $query->join('transactions','bets.transaction_id','=','transactions.id');
        $query->join('users','transactions.user_id','=','users.id');
        $query->where('transactions.origin','like','id_mobile_usher%');
        $query->whereDate('tickets.result_date', $drawDate);
        $query->where('users.is_usher', $ushersOnly);
        $query->groupBy('teller_name');
        $query->groupBy('tickets.schedule_key');
        $query->orderBy('teller_name');
        $query->orderby('tickets.schedule_key');

        $amounts = $query->get();
        
        $finalStructure = [];
        foreach ($amounts as $amount) {
            $finalStructure[$amount->user_id][$amount->schedule_key] = (float) $amount->bet_amount;
        }
        
        return $finalStructure;
    }
    
    public function getDailySalesPerOutletDateRange($dateFrom, $dateTo, $useOrigin = false, $ushersOnly = false)
    {   
        $dateFromCarbon = Carbon::createFromFormat('Y-m-d', $dateFrom, env('APP_TIMEZONE'));
        $dateToCarbon = Carbon::createFromFormat('Y-m-d', $dateTo, env('APP_TIMEZONE'));
        
        $query = BetModel::query();
        $query->select(DB::raw('bets.outlet_id,tickets.schedule_key,SUM(bets.amount) as amount'));
        $query->join('tickets','bets.ticket_id','=','tickets.id');
        
        if ($useOrigin) {
            $query->join('transactions','bets.transaction_id','=','transactions.id');
            $query->where('transactions.origin', 'like', $this->origin.'%');
            
            if ($ushersOnly) {
                $query->join('users','transactions.user_id','users.id');
                $query->where('users.is_usher', true);
            }
        }
        
        if ($dateToCarbon->gte($dateFromCarbon)) {
            $query->whereBetween('tickets.result_date', [$dateFrom, $dateTo]);
        } else {
            throw new \Exception('Please make sure that Date From is lower than Date To.');
        }
        
        $query->where('tickets.is_cancelled', false);
        $query->groupBy('bets.outlet_id');
        $query->groupBy('tickets.schedule_key');
        $query->getQuery();
        $amounts = $query->get();
        
        $finalStructure = [];
        foreach ($amounts as $amount) {
            $finalStructure[$amount->outlet_id][$amount->schedule_key] = (float) $amount->amount;
        }
        
        return $finalStructure;
    }

    public function newGetTotalAmountByDate($drawDate)
    {
        if (!isset($this->totalAmountArrayCache[$drawDate])) {
            $amounts = $this->getDrawSalesQuery($drawDate, false)->get();
            $finalStructure = [];
            foreach ($amounts as $amount) {
                $finalStructure[$amount->schedule_key]['amount'] = (double) $amount->amount;
                $finalStructure[$amount->schedule_key]['tickets_count'] = (int) $amount->tickets_count;
                $finalStructure[$amount->schedule_key]['winners_count'] = (int) $amount->winners_count;
            }
            $this->totalAmountArrayCache[$drawDate] = $finalStructure;
        }
        return $this->totalAmountArrayCache[$drawDate];
    }

    public function getGameAggregatedTotalAmountByDate($drawDate) {
        $amounts = $this->getDrawSalesQuery($drawDate, true)->get();
        $finalStructure = [];

        foreach (GamesFactory::getGameNames() as $game) {
            foreach ($amounts as $amount) {
                if ($amount->game == $game) {
                    if(!array_key_exists($amount->schedule_key, $finalStructure)) {
                        $finalStructure[$amount->schedule_key] = [];
                    }
                    $finalStructure[$amount->schedule_key][$game]['amount'] = (double) $amount->amount;
                    $finalStructure[$amount->schedule_key][$game]['tickets_count'] = (int) $amount->tickets_count;
                    $finalStructure[$amount->schedule_key][$game]['winners_count'] = (int) $amount->winners_count;
                }
                
            }
        }
        return $finalStructure;
    }

    private function getDrawSalesQuery($drawDate, $isAggregate = false) {
        
        $cache = new CachingService();
        
        $repeatition = 1;
        $games = GamesFactory::getGameNames();
        if ($isAggregate) {
            $repeatition = count($games);
        }
        
        $unionQuery = null;
        
        $drawDateCarbon = Carbon::createFromFormat('Y-m-d', $drawDate, env('APP_TIMEZONE'));
        $markDateCarbon = Carbon::createFromFormat('Y-m-d', '2018-07-14', env('APP_TIMEZONE'));
        $isReadOnly = (auth()->user()->is_read_only) ? true : false;
        
        $user = new User(auth()->user());

        for ($i=0; $i < $repeatition; $i++) {
            $query = BetModel::query();
            $gameColumn = $isAggregate ? ", bets.game " : "";
            $query->select(DB::raw('tickets.schedule_key,SUM(bets.amount) as amount,COUNT(DISTINCT bets.ticket_id) as tickets_count,COUNT(winning_tickets.bet_id) as winners_count'.$gameColumn));
            $query->join('tickets','bets.ticket_id','=','tickets.id');
            $query->join('transactions', 'bets.transaction_id','=','transactions.id');
            $query->leftJoin('winning_tickets','bets.id','=','winning_tickets.bet_id');
            
            if (!$user->isSuperAdmin()) {
                $allowedIds = [];
                foreach ($cache->getUserLeaves($user) as $sub) {
                    $allowedIds[] = $sub->id;
                }
                $allowedIds[] = $user->id();
                $query->join('users','transactions.user_id','=','users.id');
                $query->whereIn('users.id', $allowedIds);
            }
            
            if ($drawDateCarbon->gte($markDateCarbon)) {
                $query->whereDate('tickets.created_at', $drawDate);
                $query->where('tickets.result_date',$drawDate);
            } else if ($drawDateCarbon->eq($markDateCarbon->subDay())) {
                $query->where('tickets.result_date',$drawDate);
            } else {
                $query->whereDate('tickets.created_at', $drawDate);
            }
            
            $query->where('tickets.is_cancelled',false);
            if ($this->outlet) {
                $query->where('tickets.outlet_id', $this->outlet->id);
                if (!$isReadOnly) {
                    $query->where('transactions.origin', $this->origin);
                }
            } else if (array_key_exists($this->origin, Transaction::getTransactionOrigins())) {
                $query->where('transactions.origin', 'like', $this->origin.'%');
            }
            if ($isAggregate) {
                $query->where('bets.game',$games[$i]);
            }
            $query->groupBy('tickets.schedule_key');
            if ($isAggregate && $unionQuery!=null) {
                $unionQuery->union($query);
            } else {
                $unionQuery = $query;
            }
            
        }
        $unionQuery->getQuery();
        return $unionQuery;
    }

    public function countAllUnsyncedTransactions()
    {
        $count = 0;
        $isOffline = boolval(env('IS_OFFLINE', false));
        
        if ($isOffline) {
            $now = Carbon::now(env('APP_TIMEZONE'));
            $count = TransactionModel::where('sync', false)->whereDate('created_at', $now->toDateString())->count();
        }
        
        return $count;
    }
    
    public function isSoldOut($betNumber, $betAmount, $game, $type, $drawDate, $schedKey)
    {
        $cacheKey = sha1('sold_out_'.$betNumber.'_'.$game.'_'.$drawDate.'_'.$schedKey);
        if ($game == SwertresSTLGame::name()) {
            $cacheKey = sha1('sold_out_'.$betNumber.'_'.$game.'_'.$type.'_'.$drawDate.'_'.$schedKey);
        }
        $gameModel = GamesFactory::getGame($game);
        
        if (Cache::has($cacheKey)) {
            $currentAmount = Cache::get($cacheKey);
        } else {
            $query = BetModel::query();
            $query->select(DB::raw('SUM(bets.amount) as amount'));
            $query->join('tickets','bets.ticket_id','=','tickets.id');
            $query->where('bets.number', $betNumber);
            $query->where('bets.game', $game);
            if ($game == SwertresSTLGame::name()) {
                $query->where('bets.type', $type);
            }
            $query->whereDate('tickets.result_date', $drawDate);
            $query->where('tickets.schedule_key', $schedKey);
            $bets = $query->first();
            $currentAmount = (is_null($bets)) ? 0 : $bets->amount;
            Cache::put($cacheKey, $currentAmount, Carbon::tomorrow(env('APP_TIMEZONE')));
        }
        
        $isSoldOut = $gameModel->isSoldOutRule($currentAmount, $betAmount, $type);
        if (!$isSoldOut) {
            Cache::increment($cacheKey, $betAmount);
        }
        
        return $isSoldOut;
    }
    
    public static function getSeparateSales($outlet, $drawDate, $timestamp, $isUsher = false)
    {
        $query = BetModel::query();
        $query->select(DB::raw("SUM(bets.amount) as 'bet_amount', transactions.origin as 'transaction_origin', users.name as 'teller_name'"));
        $query->join('tickets','bets.ticket_id','=','tickets.id');
        $query->join('transactions', 'bets.transaction_id','=','transactions.id');
        $query->join('users', 'transactions.user_id','=','users.id');
        $query->where('transactions.outlet_id', $outlet);
        $query->where('transactions.origin', 'like', 'id_mobile_usher%');
        $query->where('tickets.result_date', $drawDate);
        if ($isUsher) {
            $query->where('users.is_usher','=', true);
        }
        $query->groupBy('transactions.origin');
        
        return $query->getQuery()->get();
    }
    
    public static function getSeparateSalesRange($outlet, $dateFrom, $dateTo, $timestamp, $isUsher = false)
    {
        $query = BetModel::query();
        $query->select(DB::raw("SUM(bets.amount) as 'bet_amount', transactions.origin as 'transaction_origin', users.name as 'teller_name'"));
        $query->join('tickets','bets.ticket_id','=','tickets.id');
        $query->join('transactions', 'bets.transaction_id','=','transactions.id');
        $query->join('users', 'transactions.user_id','=','users.id');
        $query->where('transactions.outlet_id', $outlet);
        $query->where('transactions.origin', 'like', 'id_mobile_usher%');
        $query->whereBetween('tickets.result_date', [$dateFrom, $dateTo]);
        if ($isUsher) {
            $query->where('users.is_usher','=', true);
        }
        $query->groupBy('transactions.origin');
        
        return $query->getQuery()->get();
    }
}
