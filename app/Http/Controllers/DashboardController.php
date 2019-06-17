<?php

namespace App\Http\Controllers;

use App\Bet;
use App\MobileNumber;
use App\Outlet;
use App\Payout;
use App\Ticket;
use App\Transaction;
use App\WinningResult;
use App\Http\Requests\WinningRequest;
use App\System\Data\Timeslot;
use App\System\Data\Statistics\DailySales;
use App\System\Data\Ticket as TicketData;
use App\System\Games\GamesFactory;
use App\System\Games\Pares\ParesGame;
use App\System\Games\Swertres\SwertresGame;
use App\System\Services\PayoutService;
use App\System\Services\TransactionService;
use App\System\Services\UserService;
use App\System\Utils\GeneratorUtils;
use App\System\Utils\TimeslotUtils;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Support\Facades\Input;
use App\System\Data\User;
use App\System\Services\WinningService;
use App\Jobs\ListWinnings;
use App\System\Games\Pares\ParesService;
use App\System\Data\Price;
use App\System\Games\SwertresSTL\SwertresSTLGame;
use App\Jobs\SendSmsAdmin;
use App\System\Services\SmsService;
use App\Jobs\SendSmsCustomers;
use App\System\Services\OutletService;

use App\TicketCancellation;
use App\OfflineSyncLog;
use App\Jobs\TransactionSync;
use Illuminate\Support\Facades\Hash;
use App\System\Services\CachingService;
use App\System\Games\SwertresSTLLocal\SwertresSTLLocalGame;
use App\Events\CalculatedSalesDataEvent;
use App\Events\TransactionSocketEvent;
use App\System\Utils\UserUtils;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show main dashboard.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($draw_date = null)
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }

        if (!$draw_date || !TimeslotUtils::validateDate(urldecode($draw_date))) {
            $draw_date = date('Y-m-d');
        } else {
            $draw_date = urldecode($draw_date);
        }
        $carbonObj = Carbon::createFromFormat('Y-m-d', $draw_date, env('APP_TIMEZONE'));

        $isToday = false;
        if (date('Y-m-d') == $draw_date) {
            $isToday = true;
        }
        
        $origin = request()->query('origin', null);
        if (!array_key_exists($origin, \App\System\Data\Transaction::getTransactionOrigins())) {
            $origin = null;
        }
        
        $timeslots = TimeslotUtils::getIncrementalTimeslotsUntil(0, $draw_date);

        $winningService = new WinningService();
        $cachingService = new CachingService($origin);
        $outletService = new OutletService();
        $dailySales = [];
        $numberOfTickets = [];
        $numberOfWinnings = [];
        $totalDailySales = [];
        $dailyAggregatedSales = [];
        $dailyAggregatedSalesTotals = [];
        $rawSalesData = $cachingService->getRawAmountDataByDate($draw_date);        
        $rawSalesDataGameAggregated = $cachingService->getRawAmountDataByDateAndGame($draw_date);
        $winners = $winningService->getWinningBetsStored($draw_date);
        $winnersAggregated = $winningService->winnersAggregrateByDrawDateTime($winners);
        $currentSchedKey = Timeslot::getKeyByTime(TimeslotUtils::getCurrentDrawTime(true)->format('H:i:s'));
        $winningAllocation = $winningService->totalWinningAmountAllocation($draw_date);
        foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
            $dailySales[$key] = (isset($rawSalesData[$key]['amount'])) ? $rawSalesData[$key]['amount'] : 0;
            $numberOfTickets[$key] = (isset($rawSalesData[$key]['tickets_count'])) ? $rawSalesData[$key]['tickets_count'] : 0;
            $numberOfWinnings[$key] = (isset($rawSalesData[$key]['winners_count'])) ? $rawSalesData[$key]['winners_count'] : 0;
            $dailyAggregatedSales[$key]['games'] = [];
            foreach (GamesFactory::getGameNames() as $game) {
                if (isset($rawSalesDataGameAggregated[$key])) {
                    $dailyAggregatedSales[$key]['games'][$game] = array_key_exists($game,$rawSalesDataGameAggregated[$key]) ? $rawSalesDataGameAggregated[$key][$game]['amount'] : 0;
                } else {
                    $dailyAggregatedSales[$key]['games'][$game] = 0;
                }
            }
            $dailyAggregatedSales[$key]['drawtime'] = date('g A', strtotime($timeslot));
        }
        $dailyAggregatedSalesTotals = $this->getAggregatedTotals($dailyAggregatedSales);
        $aggregatedTableGameHeaders = $this->getAllGameLabels();
        
        $userData = new User(auth()->user());
        $allowedIds = [];
        if (!$userData->isSuperAdmin()) {
            $allowedIds = UserUtils::leavesIds($userData);
        }
        
        $select_outlets_json = [];
        foreach ($outletService->getOutlets(0, 1000) as $outlet) {
            $tmp = [
                'id' => $outlet->id(),
                'name' => $outlet->name(),
            ];
            $users = [];
            foreach ($outlet->getOutlet()->tellers()->get() as $default) {
                $user = $default->user()->firstOrFail();
                $users[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            }
            $tmp['users'] = $users;
            $select_outlets_json[$outlet->id()] = $tmp;
        }

        return view('admin.dashboard', [
            'winnings' => $this->getWinnings(true, $draw_date),
            'total_amount' => $cachingService->getTotalAmountByDate($carbonObj),
            'daily_sales' => $dailySales,
            'daily_aggregated_sales' => $dailyAggregatedSales,
            'daily_aggregated_sales_totals' => $dailyAggregatedSalesTotals,
            'daily_aggregated_sales_headers' => $aggregatedTableGameHeaders,
            'number_of_tickets' => $numberOfTickets,
            'number_of_winnings' => $numberOfWinnings,
            'winners' => $winners,
            'winners_aggregated' => $winnersAggregated,
            'winning_allocation' => $winningAllocation,
            'previous_dates' => $this->generatePrettyArrayForTimeDropdown($draw_date),
            'current_drawdate' => $draw_date,
            'current_drawdate_time_pretty' => TimeslotUtils::getCurrentDrawTime(true)->format('g a'),
            'is_today' => $isToday,
            'origin' => \App\System\Data\Transaction::getTransactionOrigins(),
            'current_origin' => $origin,
            'app_key' => env('PUSHER_APP_KEY'),
            'app_cluster' => env('PUSHER_APP_CLUSTER'),
            'allowed_ids' => $allowedIds,
            'is_superadmin' => $userData->isSuperAdmin(),
            'to_show_bet_dialogue' => true,
            'available_schedules' => $this->generateTransactionInputStrings($timeslots),
            'games' => GamesFactory::getGames(),
            'select_outlets_json' => $select_outlets_json,
        ]);
    }

    /**
     * Show per outlet dashbaord
     *
     * @param $outlet_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function perOutlet($outlet_id, $draw_date = null)
    {
        if (!$draw_date || !TimeslotUtils::validateDate(urldecode($draw_date))) {
            $draw_date = date('Y-m-d');
        } else {
            $draw_date = urldecode($draw_date);
        }

        $outlet = Outlet::findOrFail($outlet_id);
        $timeslots = TimeslotUtils::getIncrementalTimeslotsUntil(0, $draw_date);

        $payoutService = new PayoutService($outlet);
        $transactionService = new TransactionService($outlet);
        $winningService = new WinningService();

        $payouts = $payoutService->getPayoutsPerDate($draw_date, 0, 100);
        $dailySales = [];
        $numberOfTickets = [];
        $numberOfWinnings = [];
        $rawSalesData = $transactionService->newGetTotalAmountByDate($draw_date);
        foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
            $dailySales[$key] = (isset($rawSalesData[$key]['amount'])) ? $rawSalesData[$key]['amount'] : 0;
            $numberOfTickets[$key] = (isset($rawSalesData[$key]['tickets_count'])) ? $rawSalesData[$key]['tickets_count'] : 0;
            $numberOfWinnings[$key] = (isset($rawSalesData[$key]['winners_count'])) ? $rawSalesData[$key]['winners_count'] : 0;
        }
        
        $prevTimeslotsForDailySales = $this->getPrevTimeslotsForDailySales($outlet, $draw_date);

        $prevTimeslotsForDailySales = [];
        $prevTimeslots = TimeslotUtils::getDecrementalTimeslotsUntil(7, $draw_date);
        foreach ($prevTimeslots as $prevTimeslot) {
            $hasTickets = $outlet->tickets()->where([
                'result_date' => $prevTimeslot['date'],
                'schedule_key' => $prevTimeslot['key'],
                'is_cancelled' => false,
            ])->count();
            if ($hasTickets > 0) {
                $prevTimeslotsForDailySales[] = $prevTimeslot;
            }
        }

        $toShowBetDialogue = false;
        $authUser = auth()->user();
        $userData = new User($authUser);
        if ($userData->role() == User::ROLE_TELLER) {
            $toShowBetDialogue = true;
        }

        $currentPrint = request()->cookie('current_print_tickets_str', false);

        return view('outlets.dashboard', [
            'outlet_name' => $outlet->name,
            'outlet_address' => $outlet->address,
            'outlet_id' => $outlet_id,
            'available_schedules' => $this->generateTransactionInputStrings($timeslots),
            'games' => GamesFactory::getGames($outlet),
            'winnings' => $this->getWinnings(false, $draw_date),
            'payouts' => $payouts,
            'daily_sales' => $dailySales,
            'number_of_tickets' => $numberOfTickets,
            'number_of_winnings' => $numberOfWinnings,
            'prev_schedules' => $this->generateTransactionInputStrings($prevTimeslotsForDailySales),
            'to_show_bet_dialogue' => $toShowBetDialogue,
            'current_print' => $currentPrint,
            'previous_dates' => $this->generatePrettyArrayForTimeDropdown($draw_date),
            'current_drawdate' => $draw_date,
            'count_unsync' => $transactionService->countAllUnsyncedTransactions(),
            'sync_logs' => $this->getSyncLogs(),
        ]);
    }

    /**f
     * Ajax endpoint for calculating winnings.
     *
     * @param $outlet_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxCalculateWinning($outlet_id)
    {
        $outlet = Outlet::findOrfail($outlet_id);
        $game = GamesFactory::getGame(\request()->input('game'), $outlet);
        $errors = [];

        if (!$game->isValidBet(\request()->input('betNumber'))) {
            $errors[] = 'Invalid bet number';
        }

        if (!is_numeric(\request()->input('amount'))) {
            $errors[] = 'Invalid amount entered';
        }

        if (is_numeric(\request()->input('amount'))) {
            $amount = (int) \request()->input('amount');
            if ($amount > 500) {
                $errors[] = 'No bet shall exceed PHP 500.00.';
            }
        }

        if (!in_array(\request()->input('type'), $game->betTypes())) {
            $errors[] = 'Invalid bet type for ' . $game->label();
        }
        
        $schedTime = \request()->input('sched_time');

        if (count($errors) > 0) {
            $response = [
                'winning' => 0,
                'errors' => $errors,
            ];
        } else {
            $response = [
                'winning' => number_format($game->price(
                    \request()->input('betNumber'),
                    \request()->input('amount'),
                    \request()->input('type')
                ), 2, '.', ','),
                'game' => GamesFactory::getGameLabelByGameName(\request()->input('game')),
                'is_sold_out' => false,
            ];
        }

        return response()
            ->json($response)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    }

    /**
     * AJAX check ticket for payout.
     *
     * @param null $outlet_id
     * @return $this
     */
    public function ajaxCheckTicketForPayout($outlet_id = null)
    {
        try {
            $ticketNumber = \request()->input('ticket-number');
            $service = new PayoutService();
            $winningBets = $service->getTicketWinningBets($ticketNumber, $outlet_id);

            $winningBetsOutput = [];
            $betIds = [];
            foreach ($winningBets as $bet) {
                $betIds[] = [
                    'bet' => $bet['obj']->getBet()->id,
                    'win' => $bet['win']->id,
                ];
                $output = [
                    'number' => str_replace(':','-', $bet['obj']->betNumber()),
                    'amount' => number_format($bet['obj']->amount(), 2, '.', ','),
                    'price' => number_format($bet['obj']->price(), 2, '.', ','),
                    'bet_type' => ucfirst($bet['obj']->betType()),
                ];
                $winningBetsOutput[] = $output;
            }

            return response()->json([
                'winning_bets' => $winningBetsOutput,
                'passbets' => (count($winningBetsOutput) > 0) ? Crypt::encrypt(json_encode($betIds)) : '',
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'winning_bets' => [],
                'win_error' => $e->getMessage(),
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        } catch (\Exception $e) {
            return response()->json([
                'winning_bets' => [],
                'win_error' => $e->getMessage(),
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        }
    }

    /**
     * Get Daily Sales via Ajax.
     *
     * @param integer $outlet_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxGetDailySales($outlet_id)
    {
        try {
            $datetime = \request()->input('datetime');
            try {
                list($drawDate, $timeslot) = explode(' ', $datetime);
                $schedKey = Timeslot::getKeyByTime($timeslot);
            } catch (\Exception $e) {
                $drawDate = $datetime;
                $schedKey = null;
            }
            $outlet = Outlet::where('id', $outlet_id)->firstOrFail();
            $dailySales = new DailySales($outlet, $drawDate, $schedKey);
            $output = $dailySales->toArray(true);

            return response()
                ->json($output)
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        }
    }

    public function ajaxDisableOutlet()
    {
        try {
            if (auth()->user()->is_read_only) {
                throw new \Exception('Read-only admins cannot disable or enable an outlet.');
            }
            
            $outlet_id = \request()->input('outlet_id');
            $to_enable = \request()->input('to_enable', false);
            $to_enable = (is_string($to_enable)) ? filter_var($to_enable, FILTER_VALIDATE_BOOLEAN) : $to_enable;
            $service = new OutletService();

            if ($to_enable) {
                $service->enableOutlet($outlet_id);
            } else {
                $service->disableOutlet($outlet_id);
            }

            return response()
                ->json(['success' => true])
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        }
    }

    /**
     * CHeck ticket cancelation.
     *
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxCheckTicketCancellation()
    {
        try {
            $ticketNumber = \request()->input('ticket-number');
            $isForced = \request()->input('force-cancel', false);
            $isForced = (is_string($isForced)) ? filter_var($isForced, FILTER_VALIDATE_BOOLEAN) : $isForced;
            $ticket = Ticket::with('transaction','outlet','bets')->where('ticket_number', trim($ticketNumber))->firstOrFail();
            $transactionData = new \App\System\Data\Transaction($ticket->outlet, $ticket->transaction);
            $ticketData = new TicketData($ticket->outlet, $ticket);

            if (!$isForced && $ticketData->isDrawTimePassed()) {
                throw new \Exception('Ticket cannot be cancelled anymore. It already has passed the draw time which is on ' . $ticketData->drawDateTimeCarbon()->toDayDateTimeString() . '.');
            }

            if ($ticketData->isCanceled()) {
                throw new \Exception('Ticket already has been canceled.');
            }

            $betsData = $ticketData->bets();

            $output = [];
            foreach ($betsData as $bet) {
                $output['bets'][] = [
                    'bet' => $bet->betNumber(),
                    'type' => ucfirst($bet->betType()),
                    'amount' => 'PHP '.number_format($bet->amount(), 2, '.', ','),
                    'draw_datetime' => $ticketData->drawDateTimeCarbon()->toDayDateTimeString(),
                    'teller' => $transactionData->teller(),
                    'outlet' => $ticket->outlet->name,
                ];
            }

            return response()
                ->json($output)
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        }
    }

    /**
     * Process and store transaction into the database.
     *
     * @param $outlet_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateTransaction($outlet_id)
    {
        $outlet = Outlet::findOrFail($outlet_id);
        $data = \request()->input('data');
        $json = json_decode($data);
        
        $user = (isset($json->user)) ? \App\User::where('id', $json->user)->firstOrFail() : auth()->user();
        
        $cachingService = new CachingService();

        try {

            // Check if outlet already has been disabled
            $outletService = new OutletService();
            if ($outletService->isDisabled($outlet)) {
                throw new \Exception('This outlet is disabled. You cannot make a bet until it is re-enabled.');
            }

            $userService = new UserService();
            if (!$userService->isAllowedBetting($user)) {
                throw new \Exception('User is now disabled for betting. You cannot make a bet until you are re-enabled.');
            }

            // Separate bets into tickets
            $tickets = $this->aggregateSubmittedBetsByTickets($json->bets);
            $paresService = new ParesService();

            // Bets must not exceed 500
            foreach ($tickets as $bets) {
                foreach ($bets as $bet) {
                    if ($bet->amount > 500) {
                        throw new \Exception("No bet shall exceed PHP 500.00.");
                    }
                }
            }
            
            $events = [];

            DB::beginTransaction();

            // Create a transaction
            $transaction = Transaction::create([
                'outlet_id' => $outlet->id,
                'user_id' => $user->id,
                'transaction_code' => GeneratorUtils::transactionCode(),
                'customer_name' => $json->transactions->customer_name,
                'origin' => 'outlet',
            ]);

            $ticketIds = [];
            
            foreach ($tickets as $datetime => $bets) {
                list($date, $time) = explode(' ', $datetime);
                $key = Timeslot::getKeyByTime($time);
                
                if (!isset($events[$key])) {
                    $events[$key] = [
                        'amount' => 0,
                        'ticket_count' => 0,
                    ];
                }

                // DIsallow transaction with Draw time already late from the current draw.
                $timeCarbon = Carbon::createFromFormat('H:i:s', $time, env('APP_TIMEZONE'))->subMinutes(TimeslotUtils::globalCutOffTime());
                if (!TimeslotUtils::timeWithinDrawTimeBracket($timeCarbon, true)) {
                    throw new \Exception('Draw time of one of the bet is not within the current draw time bracket.');
                }

                // Create tickets and relate them to the newly created transaction
                $ticket = Ticket::create([
                    'transaction_id' => $transaction->id,
                    'outlet_id' => $outlet->id,
                    'user_id' => $user->id,
                    'ticket_number' => GeneratorUtils::ticketNumber(),
                    'result_date' => $date,
                    'schedule_key' => $key,
                    'is_cancelled' => false,
                ]);
                $cachingService->setRawAmount(CachingService::CACHE_TICKETS, 1, $key, $date);

                // Create bets and relate them to the ticket
                foreach ($bets as $bet) {
                    $game = GamesFactory::getGame($bet->game, $outlet);
                    $betNumber = $bet->number;

                    // Add zero if number is below 10
                    if (ParesGame::name() == $game::name()) {
                        $betNumber = $paresService->addLeadingZero($betNumber);
                    }

                    Bet::create([
                        'transaction_id' => $transaction->id,
                        'outlet_id' => $outlet->id,
                        'ticket_id' => $ticket->id,
                        'amount' => $bet->amount,
                        'type' => $bet->type,
                        'number' => $betNumber,
                        'game' => $bet->game,
                    ]);
                    
                    $cachingService->setRawAmount(CachingService::CACHE_SALES, intval($bet->amount), $key, $date);
                    $cachingService->setRawAmountAggregated(CachingService::CACHE_SALES, intval($bet->amount), $bet->game, $key, $date);
                    $cachingService->addToTotalAmountByDate($bet->amount);
                    
                    $events[$key]['amount'] += (float) $bet->amount;
                }

                $ticketIds[] = $ticket->id;
                $events[$key]['ticket_count']++;
            }

            // Save SMS
            $mobile = \request()->input('mobileNo');
            if ($mobile) {
                try {
                    $sms = new SmsService($mobile);
                    MobileNumber::create([
                        'user_id' => $user->id,
                        'mobile_number' => $sms->filter($mobile),
                        'do_not_send' => false,
                        'role' => \App\System\Data\MobileNumber::ROLE_CUSTOMER,
                    ]);
                    $sms->sendCustomerSmsRegistration(new \App\System\Data\Outlet($outlet));
                } catch (\Exception $e1) {
                    report($e1);
                    if ($e1 instanceof QueryException || $e1 instanceof FatalThrowableError) {
                        throw $e1;
                    }
                }
            }

            DB::commit();

            $ticketIdsString = GeneratorUtils::generateStringWithDelimiter($ticketIds,':');
            $ticketIdsString = urlencode(base64_encode($ticketIdsString));
            Session::flash('outlet-dashboard-success', 'Bet has been created successfully.');
            
            $this->dispatch(new TransactionSync());
            event(new TransactionSocketEvent($outlet, $transaction));
            foreach ($events as $bet_schedule => $event) {
                event(new CalculatedSalesDataEvent($user, $event['amount'], $event['ticket_count'], $bet_schedule));
            }
            
            if (auth()->user()->is_admin && !auth()->user()->is_read_only) {
                return redirect()->route('dashboard');
            }

            return redirect()->route('multiple-receipts', [
                'tickets_str' => $ticketIdsString,
                'pos' => 0,
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage(), $outlet);
        } catch (FatalThrowableError $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage(), $outlet);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->redirectWithErrorFlash($e->getMessage(), $outlet);
        }
    }

    /**
     * Process and store winning results.
     *
     * @param WinningRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postInsertWinning(WinningRequest $request)
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }

        try {
            $timeslot       = $request->input('winning-timeslot');
            $swertres       = $request->input(SwertresGame::name().'-result1', null);
            $swertresSTL    = $request->input(SwertresSTLGame::name().'-result1', null);
            $swertresSTLL   = $request->input(SwertresSTLLocalGame::name().'-result1', null);
            $pares1         = $request->input(ParesGame::name().'-result1', null);
            $pares2         = $request->input(ParesGame::name().'-result2', null);
            $schedKey       = Timeslot::getKeyByTime($timeslot);
            $user           = auth()->user();

            if (!$swertres && !$swertresSTL && !$swertresSTLL && !$pares1 && !$pares2) {
                throw new \Exception("Please input either Swertres Nat't, Swer3 STL, or Swer3 Local. Do not leave all results blank.");
            }

            DB::beginTransaction();

            $this->swertresWinning($user, $schedKey, $swertres);
            $this->swertresSTLWinning($user, $schedKey, $swertresSTL);
            $this->swertresSTLLocalWinning($user, $schedKey, $swertresSTLL);
            $this->paresWinning($user, $schedKey, $pares1, $pares2);

            DB::commit();

            $this->dispatch(new ListWinnings(date('Y-m-d'), $schedKey));
            /* $this->dispatch(new SendSmsAdmin(date('Y-m-d'), $schedKey));
            $this->dispatch(new SendSmsCustomers(date('Y-m-d'), $schedKey)); */

            Session::flash('dashboard-success', 'Winning results has been set.');

            return redirect()->route('dashboard');
        } catch (QueryException $e) {
            DB::rollBack();
            report($e);
            return $this->redirectWithErrorFlash($e->getMessage());
        } catch (FatalThrowableError $e) {
            DB::rollBack();
            report($e);
            return $this->redirectWithErrorFlash($e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return $this->redirectWithErrorFlash($e->getMessage());
        }
    }

    /**
     * Save bet payout.
     *
     * @param null $outlet_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSavePayouts($outlet_id = null)
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        try {
            $passbets = \request()->input('passbets');
            if (strlen($passbets) <= 0) {
                throw new \Exception('Please make sure that there is a winning bet in the ticket.');
            }

            $betIdsJson = Crypt::decrypt($passbets);
            $betIds = json_decode($betIdsJson);

            $betIdsWhIn = [];
            foreach ($betIds as $bid) {
                $betIdsWhIn[] = $bid->bet;
            }

            $bets = Bet::whereIn('id', $betIdsWhIn)->get();
            $toInsert = [];
            $outlet = $bets[0]->outlet()->first();

            $totalWin = 0;

            foreach ($bets as $bet) {

                $betData = new \App\System\Data\Bet($outlet, $bet);
                $totalWin += $betData->price();

                if ($outlet_id && $totalWin > 10000) {
                    throw new \Exception('Outlets cannot payout total prices more than PHP 10,000. Please refer the winning bettor to the main office.');
                }

                $toInsert[] = [
                    'user_id' => auth()->user()->id,
                    'outlet_id' => $outlet->id,
                    'transaction_id' => $bet->transaction_id,
                    'ticket_id' => $bet->ticket_id,
                    'bet_id' => $bet->id,
                    'winning_result_id' => $this->findWinIdByBetIdArrayFromJson($bet->id, $betIds),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }

            Payout::insert($toInsert);
            Session::flash('dashboard-success', 'Payout successful.');

            if (auth()->user()->is_admin) {
                return redirect()->route('dashboard');
            }
            return redirect()->route('outlet-dashboard', ['outlet_id' => $outlet->id]);
        } catch (DecryptException $e) {
            $outlet = (!empty($outlet_id)) ? Outlet::where(['id' => $outlet_id])->firstOrFail() : null;
            return $this->redirectWithErrorFlash($e->getMessage(), $outlet);
        } catch (QueryException $e) {
            $outlet = (!empty($outlet_id)) ? Outlet::where(['id' => $outlet_id])->firstOrFail() : null;
            return $this->redirectWithErrorFlash($e->getMessage(), $outlet);
        } catch (\Exception $e) {
            $outlet = (!empty($outlet_id)) ? Outlet::where(['id' => $outlet_id])->firstOrFail() : null;
            return $this->redirectWithErrorFlash($e->getMessage(), $outlet);
        }
    }

    /**
     * Cancel ticket.
     *
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCancelTicket($returnDataOnly = false, $fromApi = false)
    {
        if (!auth()->user()->is_admin && !$fromApi) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        try {
            $ticketNumber = $returnDataOnly ? Input::get('ticket_number') : \request()->input('ticket-number');
            $isForced = \request()->input('force-cancel', false);
            $isForced = (is_string($isForced)) ? filter_var($isForced, FILTER_VALIDATE_BOOLEAN) : $isForced;
            
            DB::beginTransaction();
            
            $ticket = Ticket::with('transaction','outlet','bets')->where('ticket_number', trim($ticketNumber))->firstOrFail();
            $ticketData = new TicketData($ticket->outlet, $ticket);
            
            if ('3a8gaming@stl.ph' !== auth()->user()->email && !$fromApi) {
                throw new \Exception('Only 3a8 Gaming can cancel.');
            }
            
            if ($ticketData->isCanceled()) {
                throw new \Exception('Ticket already has been canceled.');
            }

            if ($returnDataOnly && $fromApi) {
                if (auth()->user()->status != "active") {
                    throw new \Exception('Account is inactive.');
                }

                if (!$ticketData->isCancellableViaApi()) {
                    throw new \Exception('Ticket cannot be cancelled anymore. It already has passed time limit');
                }
            }

            if (!$isForced && $ticketData->isDrawTimePassed()) {
                throw new \Exception('Ticket cannot be cancelled anymore. It already has passed the draw time which is on ' . $ticketData->drawDateTimeCarbon()->toDayDateTimeString() . '.');
            }

            $ticket->is_cancelled = true;
            $ticket->save();
            
            TicketCancellation::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->user()->id,
            ]);
            
            DB::commit();
            
            if ($returnDataOnly) {
                return [ 'success' => true, 'message' => "" ];
            }

            Session::flash('dashboard-success', 'Ticket cancellation success.');
            return redirect()->route('dashboard');
        } catch (DecryptException $e) {
            DB::rollBack();
            if ($returnDataOnly) {
                return [ 'success' => false, 'message' => "Decrypt Error" ];
            }
            return $this->redirectWithErrorFlash($e->getMessage());
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            if ($returnDataOnly) {
                return [ 'success' => false, 'message' => "Data not found" ];
            }
            return $this->redirectWithErrorFlash($e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            if ($returnDataOnly) {
                return [ 'success' => false, 'message' => $e->getMessage() ];
            }
            return $this->redirectWithErrorFlash($e->getMessage());
        }
    }
    
    public function changePassword($outlet_id = null)
    {
        try {
            
            $outlet = ($outlet_id) ? Outlet::where('id', $outlet_id)->first() : null;
            
            $oldPassword = \request()->input('old-password');
            $newPassword = \request()->input('new-password');
            $newPasswordConfirm = \request()->input('new-password-confirm');
            
            $userService = new UserService();
            $userService->updatePassword($oldPassword, $newPassword, $newPasswordConfirm);

            if (auth()->user()->is_admin) {
                Session::flash('dashboard-success', 'Your password has been updated.');
                return redirect()->route('dashboard');
            }
            
            Session::flash('outlet-dashboard-success', 'Your password has been updated.');
            return redirect()->route('outlet-dashboard', ['outlet_id' => $outlet->id]);
                
        } catch (\Exception $e) {
            return $this->redirectWithErrorFlash($e->getMessage(), $outlet);
        }
    }
    /*
     * View sync logs
     * @return view
     *
     */
    private function getSyncLogs($date = "" )
    {
        if (!env('IS_OFFLINE', false)) {
            return [];
        }
        
        if (empty($date)) {
            $date = Carbon::now();
        }
        $dt = Carbon::createFromFormat('Y-m-d H:i:s', $date, env('APP_TIMEZONE'));
        $strDate = $dt->toDateString();
        
        $offlineSyncLogs = OfflineSyncLog::where('sync_time','>=', $strDate)->orderBy('sync_time', 'desc')->limit(50)->get()->toArray();
        return $offlineSyncLogs;
    }

    /**
     * Generate transaction input strings.
     *
     * @param array $timeslots
     * @return array
     */
    private function generateTransactionInputStrings(array $timeslots)
    {
        $strings = [];
        foreach ($timeslots as $timeslot) {
            $strings[$timeslot['date'] . ' ' . $timeslot['time']] = Carbon::parse($timeslot['date'] . ' ' . $timeslot['time'])->toDayDateTimeString();
        }
        return $strings;
    }

    /**
     * Aggregate submitted bets by tickets.
     *
     * @param array $bets
     * @return array
     */
    private function aggregateSubmittedBetsByTickets(array $bets)
    {
        $tickets = [];
        $datescheds = [];

        foreach ($bets as $bet) {
            if (! in_array($bet->bet_schedule_input, $datescheds)) {
                $datescheds[] = $bet->bet_schedule_input;
            }
        }

        foreach ($datescheds as $dsched) {
            foreach ($bets as $bet) {
                if ($dsched === $bet->bet_schedule_input) {
                    $tickets[$dsched][] = $bet;
                }
            }
        }

        return $tickets;
    }

    /**
     * Redirect with error flash.
     *
     * @param $message
     * @param null $outlet
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectWithErrorFlash($message, $outlet = null)
    {
        Session::flash('error-flash', $message);
        if (auth()->user()->is_admin) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('outlet-dashboard', ['outlet_id' => $outlet->id]);
    }

    /**
     * Get winnings per timeslots.
     *
     * @return array
     */
    private function getWinnings($isAdmin = false, $draw_date = null)
    {
        $winnings = [];
        $drawDate = ($draw_date && strlen($draw_date) > 0) ? $draw_date : date('Y-m-d');
        $payoutService = new PayoutService();
        foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
            foreach (GamesFactory::getGameNames() as $gameName) {
                $gameLabel = GamesFactory::getGameLabelByGameName($gameName);
                $winning = $payoutService->getWinningResult($gameName, $drawDate, $key);
                if ($isAdmin) {
                    $winnings[$key][$gameLabel] = $winning;
                } else {
                    $winnings[$key][$gameLabel] = (TimeslotUtils::isDrawTimePassed($drawDate, $key)) ? $winning : '';
                }
            }
        }

        return $winnings;
    }

    /**
     * Store winning result for swertres.
     *
     * @param $user
     * @param $schedKey
     * @param $result1
     * @throws \Exception
     */
    private function swertresWinning($user, $schedKey, $result1)
    {
        $this->swertresCommonWinning($user, $schedKey, $result1, SwertresGame::name());
    }

    private function swertresSTLWinning($user, $schedKey, $result1)
    {
        $this->swertresCommonWinning($user, $schedKey, $result1, SwertresSTLGame::name());
    }
    
    private function swertresSTLLocalWinning($user, $schedKey, $result1)
    {
        $this->swertresCommonWinning($user, $schedKey, $result1, SwertresSTLLocalGame::name());
    }

    private function swertresCommonWinning($user, $schedKey, $result1, $game)
    {
        if (!$result1 || strlen($result1) <= 0) {
            return;
        }

        $existing = WinningResult::where('result_date', '=', date('Y-m-d'))
            ->where('schedule_key', '=', $schedKey)
            ->where('game', '=', $game)
            ->first();

        if ($existing) {
            $existing->number = $result1;
            $existing->save();
        } else {
            WinningResult::create([
                'user_id' => $user->id,
                'game' => $game,
                'number' => $result1,
                'result_date' => date('Y-m-d'),
                'schedule_key' => $schedKey,
            ]);
        }
    }

    /**
     * Store winning results for pares.
     *
     * @param $user
     * @param $schedKey
     * @param $result1
     * @param $result2
     * @throws \Exception
     */
    private function paresWinning($user, $schedKey, $result1, $result2)
    {
        if ((((!$result1 || strlen($result1)) <= 0) && $result2) || ($result1 && (!$result2 || strlen($result2) <= 0))) {
            throw new \Exception('If you supply results for Pares, please input both numbers.');
        }

        if (!$result1 && !$result2) {
            return;
        }

        $service = new ParesService();
        $result = $service->addLeadingZero(sprintf("%s:%s", $result1, $result2));
        $existing = WinningResult::where('result_date', '=', date('Y-m-d'))
            ->where('schedule_key', '=', $schedKey)
            ->where('game', '=', ParesGame::name())
            ->first();

        if ($existing) {
            $existing->number = $result;
            $existing->save();
        } else {
            WinningResult::create([
                'user_id' => $user->id,
                'game' => ParesGame::name(),
                'number' => $result,
                'result_date' => date('Y-m-d'),
                'schedule_key' => $schedKey,
            ]);
        }
    }

    /**
     * Find winning ID by bet ID array from JSON.
     *
     * @param $needle
     * @param array $fromJson
     * @return mixed
     * @throws \Exception
     */
    private function findWinIdByBetIdArrayFromJson($needle, array $fromJson)
    {
        foreach ($fromJson as $bet) {
            if ($needle == $bet->bet) {
                return $bet->win;
            }
        }
        throw new \Exception('Bet ID not found when looking for winning ID.');
    }

    private function generatePrettyArrayForTimeDropdown()
    {
        $prettyArray = [];
        $pointDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
        for ($i = 0; $i <= 7; $i++) {
            if ($i > 0) {
                $pointDate->subDays(1);
            }
            if (date('Y-m-d') == $pointDate->toDateString()) {
                $prettyArray[$pointDate->toDateString()] = 'Today';
            } else {
                $prettyArray[$pointDate->toDateString()] = $pointDate->toFormattedDateString();
            }
        }

        return $prettyArray;
    }
    
    private function getPrevTimeslotsForDailySales($outlet, $drawDate, $days = 7)
    {
        $prevTimeslotsForDailySales = [];
        $prevTimeslots = TimeslotUtils::getDecrementalTimeslotsUntil($days, $drawDate);
        $carbonFrom = Carbon::createFromFormat('Y-m-d', $drawDate, env('APP_TIMEZONE'))->subDays($days);
        $carbonTo = Carbon::createFromFormat('Y-m-d', $drawDate, env('APP_TIMEZONE'));
        $tickets = $outlet->tickets()->whereBetween('result_date', [$carbonFrom, $carbonTo])->get();
        
        foreach ($prevTimeslots as $prevTimeslot) {
            $count = 0;
            foreach ($tickets as $ticket) {
                if ($ticket->result_date == $prevTimeslot['date']
                && $ticket->schedule_key == $prevTimeslot['key']
                && $ticket->is_cancelled == false) {
                    $count++;
                }
            }
            
            if ($count > 0) {
                $prevTimeslotsForDailySales[] = $prevTimeslot;
            }
        }
        
        return $prevTimeslotsForDailySales;
    }

    private function getAggregatedTotals($data) {
        $totals = [];
        foreach (GamesFactory::getGameNames() as $game) {
            $totals[$game] = 0;
            foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
                $totals[$game] += $data[$key]['games'][$game];
            }
        }
        return $totals;
    }

    private function getAllGameLabels() {
        $labels = [];
        foreach (GamesFactory::getGameNames() as $gameName) {
            $labels[$gameName] = GamesFactory::getGameLabelByGameName($gameName);
        }
        return $labels;
    }
}
