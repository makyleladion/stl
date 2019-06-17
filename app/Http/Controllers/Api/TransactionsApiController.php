<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

use Carbon\Carbon;

use App\Http\Controllers\Controller;

use App\System\Data\User;
use App\System\Data\Timeslot;
use App\System\Games\GamesFactory;
use App\System\Games\Pares\ParesGame;
use App\System\Games\Pares\ParesService;
use App\System\Services\OutletService;
use App\System\Services\PayoutService;
use App\System\Services\TransactionFilter;
use App\System\Services\TransactionService;
use App\System\Services\UserService;
use App\System\Services\WinningService;
use App\System\Utils\GeneratorUtils;
use App\System\Utils\TimeslotUtils;

use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\App;

use App\Bet;
use App\Outlet;
use App\Transaction;
use App\Ticket;
use App\InvalidTicket;
use App\ApiLog;

use App\System\Data\Transaction as TransactionData;
use App\Events\CalculatedSalesDataEvent;


class TransactionsApiController extends Controller
{
    private $outlet;

    public function index()
    {
        $data = Input::all();
        $user = auth()->user();
        $user_id = array_key_exists("user_id", $data) ? $data['user_id'] : $user->id;
        $outlet_id = $data['outlet_id'];
        $origin = array_key_exists("origin", $data) ? $data['origin'] : "";

        $datetimeNow =  Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now()->toDateTimeString(), env('APP_TIMEZONE'));

        $data['created_at'] = $datetimeNow->toDateTimeString();
        $data['updated_at'] = $datetimeNow->toDateTimeString();
        
        $this->logApiRequest($data);

        $isValid = $this->validateInput($data);
        $invalidTickets = [];
        $invalidTicketsData = [];
        $validTickets = [];

        if (is_array($isValid)) {
            if (!$isValid['result'] && array_key_exists("invalid_tickets",$isValid)) {
                $invalidTickets = $isValid['invalid_tickets'];
                foreach ($isValid['invalid_tickets'] as $invalid_ticket_number) {
                    for( $i=0; $i<count($data['tickets']); $i++) {
                        if (is_array($data['tickets'][$i]) && array_key_exists('ticket_number', $data['tickets'][$i]['data'])) {
                            if ($invalid_ticket_number['ticket_number'] == $data['tickets'][$i]['data']['ticket_number']) {
                                $tkToAdd = $data['tickets'][$i]['data'];
                                $tkToAdd['error'] = $invalid_ticket_number['error'];
                                $tkToAdd['user_id'] = $user_id;
                                $tkToAdd['outlet_id'] = $outlet_id;
                                if (array_key_exists("draw_date", $tkToAdd)) {
                                    list($dateTk, $timeTk) = explode(' ', $tkToAdd['draw_date']);
                                    $tkToAdd['result_date'] = $dateTk;
                                    $tkToAdd['schedule_key'] = Timeslot::getKeyByTime($timeTk);
                                } else {
                                    $tkToAdd['result_date'] = "";
                                    $tkToAdd['schedule_key'] = "";
                                }
                                $tkToAdd['source'] = $origin;
                                $invalidTicketsData[] = $tkToAdd;
                                unset($data['tickets'][$i]);
                                $data['tickets'] = array_values(array_filter($data['tickets']));
                                $i--;
                            }
                        }
                    }
                }
            } else if (!$isValid['result'] && array_key_exists("message",$isValid)) { 
                return response()->json([ 'success' => [], 'failed' => [], 'message' => $isValid['message'] ]);
            }
        }
        
        try {
            $transaction_code =  array_key_exists("transaction_number", $data) ? $data['transaction_number'] : $data['transaction_code'];
        } catch (\Exception $e) {
            $transaction_code = "";
        }
        
        $this->captureInvalidTickets($invalidTicketsData, $transaction_code);
        
        if (!$isValid) {
            $str = json_encode(array('status' => false, 'message' => 'Missing payload parameters'));
            return response()->json($str);
        }
        // Transaction
        $transaction_id_to_sync = array_key_exists("transaction_number", $data) ? $data['transaction_number'] : $data['transaction_id'];
        $transaction_code =  array_key_exists("transaction_number", $data) ? $data['transaction_number'] : $data['transaction_code'];
        $customer_name = $data['customer_name'];
        $created_at = $data['created_at'];
        $updated_at = $data['updated_at'];

        $uniqueTxCode = true;
        while($uniqueTxCode) {
            $existingTxn = Transaction::where('transaction_code', '=', $transaction_code)->get()->toArray();
            if (count($existingTxn)) {
                $transaction_code = GeneratorUtils::transactionCode();
            } else {
                $uniqueTxCode = false;
            }
        }

        // Insert transaction
        $tx = new Transaction();
        $tx->user_id = $user_id;
        $tx->outlet_id = $outlet_id;
        $tx->transaction_code = $transaction_code;
        $tx->customer_name = $customer_name;
        $tx->created_at = $created_at;
        $tx->updated_at = $updated_at;
        $tx->origin = $origin;
        $tx->save();
        $transaction_id = $tx->id;

        // Ticket
        $tickets = $data['tickets'];

        foreach ($tickets as $ticket) {

            if (array_key_exists("data", $ticket)) {
                $ticket = $ticket["data"];
            }

            $uniqueTxCode = true;
            while($uniqueTxCode) {
                $existingTxn = Ticket::where('ticket_number', '=', $ticket['ticket_number'])->get()->toArray();
                if (count($existingTxn)) {
                    $ticket['ticket_number'] = GeneratorUtils::ticketNumber();
                } else {
                    $uniqueTxCode = false;
                }
            }

            if (array_key_exists("draw_date", $ticket)) {
                list($date, $time) = explode(' ', $ticket['draw_date']);
                $ticket['result_date'] = $date;
                $ticket['schedule_key'] = Timeslot::getKeyByTime($time);
            }

            $ticketToSave = new Ticket();
            $ticketToSave->user_id = $user_id;
            $ticketToSave->outlet_id = $outlet_id;
            $ticketToSave->transaction_id = $transaction_id;
            $ticketToSave->ticket_number = $ticket['ticket_number'];
            $ticketToSave->result_date = $ticket['result_date'];
            $ticketToSave->schedule_key = $ticket['schedule_key'];
            $ticketToSave->is_cancelled = array_key_exists("is_cancelled",$ticket) ? $ticket['is_cancelled'] : 0;
            $ticketToSave->created_at = $created_at;
            $ticketToSave->updated_at = $updated_at;
            $ticketToSave->save();
            $ticket_id = $ticketToSave->id;
            $validTickets[] = $ticketToSave->ticket_number;
            // Bets
            $bets = $ticket['bets'];
            foreach ($bets as $bet) {
                
                $bet['game'] = array_key_exists("bet_game", $bet) ? $bet['bet_game'] : $bet['game'];
                $bet['amount'] = array_key_exists("bet_amount", $bet) ? $bet['bet_amount'] : $bet['amount'];
                $bet['type'] = array_key_exists("bet_type_abbreviation", $bet) ? $bet['bet_type_abbreviation'] : (!empty($bet['type']) ? $bet['type'] : "none");
                $bet['number'] = array_key_exists("bet_number", $bet) ? $bet['bet_number'] : $bet['number'];

                $bet['type'] = strtoupper($bet['type']) == "S" ? "straight" : (strtoupper($bet['type']) == "R" ? "rambled" : $bet['type']);

                $betToSave = new Bet();
                $betToSave->outlet_id = $outlet_id;
                $betToSave->transaction_id = $transaction_id;
                $betToSave->ticket_id = $ticket_id;
                $betToSave->amount = $bet['amount'];
                $betToSave->type = $bet['type'];
                $betToSave->number = $bet['number'];
                $betToSave->game = $bet['game'];
                $betToSave->created_at = $created_at;
                $betToSave->updated_at = $updated_at;
                $betToSave->save();
            }

        }

        $txnSaved = [ 
            'success' => [ $validTickets ],
            'failed' => [ $invalidTickets ]
        ];

        return response()->json($txnSaved);
    }

    private function validateInput($data)  {
        $failedTickets = [];
        $hasPassed = true;

        // data integrity validation
        if (!array_key_exists("tickets", $data)) {
            return [ 'result' => false, 'invalid_tickets' => $failedTickets ];
        }

        // source validation 
        try {
            if (!array_key_exists("outlet_id",$data)) {
                throw new \Exception("No outlet_id");
            }
            $outlet = Outlet::find($data['outlet_id']);
            $outletService = new OutletService();
            if (!$outlet) {
                return [ 'result' => false, 'message' => "Outlet not found" ];
            }
        } catch (\Exception $e) {
            return [ 'result' => false, 'message' => "Outlet not found" ];
        }
        
        $user = auth()->user();
        if ($user->status != User::STATUS_ACTIVE) {
            return [ 'result' => false, 'message' => 'User is inactive' ];
        }
        $userService = new UserService();
        if (!$userService->isAllowedBetting($user)) {
            return [ 'result' => false, 'message' => 'User is now disabled for betting.' ];
        }
        
        // Cutoff validation
        if (array_key_exists("tickets", $data)) {
            $tickets = $data['tickets'];
            foreach ($tickets as $ticket) {
                if (array_key_exists("data", $ticket)) {
                    $ticket = $ticket["data"];
                }
                $thisInstanceHasPassed = true;
                $datetime = $ticket['draw_date'];
                list($date, $time) = explode(' ', $datetime);
                $key = Timeslot::getKeyByTime($time);
                $timeCarbon = Carbon::createFromFormat('H:i:s', $time, env('APP_TIMEZONE'))->subMinutes(TimeslotUtils::globalCutOffTime());
                if ((!TimeslotUtils::timeWithinDrawTimeBracket($timeCarbon, true) && TimeslotUtils::dateToday() == $date ) || TimeslotUtils::isDrawTimePassed($datetime)) {
                    $failedTickets[] = $this->returnUnsyncTicket($ticket['ticket_number'], "Passed cutoff" );
                    $hasPassed = false;
                    $thisInstanceHasPassed = false;
                }

                if ($thisInstanceHasPassed) {
                    $bets = $ticket['bets'];
                    foreach ($bets as $bet) {
                        if ($bet['bet_amount'] > 500) {
                            $failedTickets[] = $this->returnUnsyncTicket($ticket['ticket_number'], "Bet exceeded 500." );
                            $hasPassed = false;
                            $thisInstanceHasPassed = false;
                            break;
                        }
                    }
                }

            }
        }
        if (!$hasPassed) {
            return [ 'result' => false, 'invalid_tickets' => $failedTickets ];
        }

        return $hasPassed;
    }

    private function returnUnsyncTicket($ticket_number, $error) {
        return [ 'ticket_number' => $ticket_number, 'error' => $error ];
    }

    public function perOutlet(Request $request)
    {
        
        $data = Input::all();
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE); 
        $outlet_id = $data['outlet_id'];
        $draw_date = isset($data['draw_date']) ? $data['draw_date'] : null;
        $origin = request()->query('origin');

        if (!$draw_date || !TimeslotUtils::validateDate(urldecode($draw_date))) {
            $draw_date = date('Y-m-d');
        } else {
            $draw_date = urldecode($draw_date);
        }

        $authUser = auth()->user();
        $userData = new User($authUser);

        $outlet = Outlet::findOrFail($outlet_id);
        $timeslots = TimeslotUtils::getIncrementalTimeslotsUntil(0, $draw_date);
        
        $payoutService = new PayoutService($outlet);
        $transactionService = new TransactionService($outlet, $origin);
        $winningService = new WinningService();

        $payouts = $payoutService->getPayoutsPerDate($draw_date, 0, 100, true, $authUser->id);
        $payoutsUnpaginated = $payoutService->getPayoutsPerDate($draw_date, 0, 0, false, $authUser->id);

        $payoutsUnpaginatedParsed = [];

        foreach ($payoutsUnpaginated as $payoutData) {
            $payoutsUnpaginatedParsed[] = [
                'ticket_number' => $payoutData->ticketNumber(),
                'bet_number' => $payoutData->bet()->betNumber(),
                'price' => number_format($payoutData->bet()->price(), 2, '.', ','),
                'draw_datetime' => $payoutData->drawDateTime()->toDayDateTimeString(),
                'payout_datetime' => $payoutData->payoutDateTime()->toDayDateTimeString()
            ];
        }


        $dailySales = [];
        $numberOfTickets = [];
        $numberOfWinnings = [];
        $ticketsList = [];
        $ticketsCancelledList = [];

        $request->merge([
            'datefrom' => $draw_date,
            'dateto' => $draw_date,
            ]);
        
        foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
            $dailySales[$key] = $transactionService->dailySales($draw_date, $key, true, $authUser->id);
            $numberOfTickets[$key] = $transactionService->getTotalTickets($draw_date, $key, $authUser->id, false, $authUser->is_admin);
            $numberOfWinnings[$key] = $winningService->countWinnings($draw_date, $key, $outlet, $authUser->id);
            $ticketsList[$key] = $transactionService->getTickets($draw_date, $key, $authUser->id, false, $authUser->is_admin)->get()->toArray();
            $ticketsCancelledList[$key] = $transactionService->getTickets($draw_date, $key, $authUser->id, true, $authUser->is_admin)->get()->toArray();
        }

        // retrieve cancelled
        $cancelledTxFilter = new TransactionFilter();
        $cancelledTxFilter->setDateRange($draw_date, $draw_date);
        $cancelledTxFilter->setSpecificOutletById($outlet_id);
        $cancelledTxFilter->setIsCanceled(true);
        if (!$authUser->is_admin) {
            $cancelledTxFilter->setSpecificTellerById($userData->id());
        }
        $totalCountQuery = Transaction::query();
        $cancelledCount = $cancelledTxFilter->execute($totalCountQuery)->count();

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
        
        if ($userData->role() == User::ROLE_TELLER) {
            $toShowBetDialogue = true;
        }

        $currentPrint = request()->cookie('current_print_tickets_str', false);



        $str = [
            'outlet_name' => $outlet->name,
            'outlet_address' => $outlet->address,
            'outlet_id' => $outlet_id,
            'available_schedules' => $this->generateTransactionInputStrings($timeslots),
            'games' => GamesFactory::getGames($outlet),
            'winnings' => $this->getWinnings(false, $draw_date),
            'payouts' => $payoutsUnpaginatedParsed,
            'daily_sales' => $dailySales,
            'number_of_tickets' => $numberOfTickets,
            'tickets' => $ticketsList,
            'number_of_winnings' => $numberOfWinnings,
            'to_show_bet_dialogue' => $toShowBetDialogue,
            'previous_dates' => $this->generatePrettyArrayForTimeDropdown($draw_date),
            'current_drawdate' => $draw_date,
            'timeslots' => Timeslot::drawTimeslots(),
            'number_of_cancelled_tickets' => $cancelledCount,
            'cancelled_tickets' => $ticketsCancelledList,
            'number_of_payouts' => count($payoutsUnpaginated)
        ];

        return response()->json($str);
    }

    private function generateTransactionInputStrings(array $timeslots)
    {
        $strings = [];
        foreach ($timeslots as $timeslot) {
            $strings[$timeslot['date'] . ' ' . $timeslot['time']] = Carbon::parse($timeslot['date'] . ' ' . $timeslot['time'])->toDayDateTimeString();
        }
        return $strings;
    }

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
                    $winnings[$key][$gameLabel] =   (TimeslotUtils::isDrawTimePassed($drawDate, $key)) ? (strlen($winning) ? $winning : "TBD") : "TBD";
                }
            }
        }

        return $winnings;
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

    // TODO these are just recycled codes from DashboardController. It would be nice if most of these codes will be in some utils folder for code reusability.

    public function postCreateTransaction()
    {

        $data = Input::all();
        $data = $data['data'];
        $json =  $data;
        $outlet_id = $data['outlet_id'];
        $outlet = Outlet::findOrFail($outlet_id);
        $user = auth()->user();
        $ticketsToSend = [];
        try {

            $outletService = new OutletService();
            if ($outletService->isDisabled($outlet)) {
                  return json_encode([ 'error' => "Outlet is disabled"]);
            }

            $userService = new UserService();
            if (!$userService->isAllowedBetting($user)) {
                return json_encode([ 'error' => 'User is now disabled for betting.']);
            }
            
            $tickets = $this->aggregateSubmittedBetsByTickets($json['bets']);
            $paresService = new ParesService();

            // Bets must not exceed 500
            foreach ($tickets as $bets) {
                foreach ($bets as $bet) {
                    if ($bet['amount'] > 500) {
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
                'customer_name' => $json['transactions']['customer_name'],
                'origin' => $json['origin'],
            ]);
            $transactionData = new TransactionData($outlet, $transaction);
            $outletName = $transactionData->outletName();
            $transactionNumber = $transactionData->transactionNumber();

            $ticketIds = [];
            foreach ($tickets as $datetime => $bets) {

                $tempTicketData = [];

                list($date, $time) = explode(' ', $datetime);
                $key = Timeslot::getKeyByTime($time);
                
                if (!isset($events[$key])) {
                    $events[$key] = [
                        'amount' => 0,
                        'ticket_count' => 0,
                    ];
                }

                $timeCarbon = Carbon::createFromFormat('H:i:s', $time, env('APP_TIMEZONE'))->subMinutes(TimeslotUtils::globalCutOffTime());
                
                if (!TimeslotUtils::timeWithinDrawTimeBracket($timeCarbon, true) || TimeslotUtils::isDrawTimePassed($datetime)) {
                    $result_message = 'Draw time of one of the bets is not within the current draw time bracket.';
                    return response()->json([ 'error' => $result_message]);
                }

                $ticket = Ticket::create([
                    'transaction_id' => $transaction->id,
                    'outlet_id' => $outlet->id,
                    'user_id' => $user->id,
                    'ticket_number' => GeneratorUtils::ticketNumber(),
                    'result_date' => $date,
                    'schedule_key' => $key,
                    'is_cancelled' => false,
                ]);
                $ticketData = new \App\System\Data\Ticket($outlet, $ticket);
                $tempTicketData['draw_date'] = $ticketData->drawDateTimeCarbon()->toDayDateTimeString();
                $tempTicketData['ticket_number'] = $ticketData->ticketNumber();
                $tempTicketData['bet_date'] = $ticketData->betDateTime()->toFormattedDateString();
                $tempTicketData['bet_time'] = $ticketData->betDateTime()->format('h:i A');
                $tempTicketData['bets'] = [];
                foreach ($bets as $bet) {
                    $game = GamesFactory::getGame($bet['game'], $outlet);
                    $betNumber = $bet['number'];
                    $tempBetData = [];
                    if (ParesGame::name() == $game::name()) {
                        $betNumber = $paresService->addLeadingZero($betNumber);
                    }

                    $cBet = Bet::create([
                        'transaction_id' => $transaction->id,
                        'outlet_id' => $outlet->id,
                        'ticket_id' => $ticket->id,
                        'amount' => $bet['amount'],
                        'type' => $bet['type'],
                        'number' => $betNumber,
                        'game' => $bet['game'],
                    ]);
                    $betData = new \App\System\Data\Bet($outlet, $cBet);
                    $tempBetData['bet_number'] = str_replace(':', ' ', $betData->betNumber(true));
                    $tempBetData['bet_type_abbreviation'] = $betData->betTypeAbbreviation();
                    if ($tempBetData['bet_type_abbreviation']=="R" || $tempBetData['bet_type_abbreviation']=="R (STL)" 
                            || $tempBetData['bet_type_abbreviation']=="R (Local)") {
                      $tempBetData['bet_number'] = str_replace( "<span class=\"bet-perm-bet-num\">" ,'',$tempBetData['bet_number']);
                      $tempBetData['bet_number'] = str_replace( "</span>" ,'',$tempBetData['bet_number']);
                    }

                    $tempBetData['bet_amount'] = $betData->amount();
                    $tempBetData['bet_price'] = number_format($betData->price(), 2, '.', ',');
                    $tempTicketData['bets'][] = $tempBetData;
                    
                    $events[$key]['amount'] += (float) $bet['amount'];
                }

                $ticketIds[] = $ticket->id;
                $tempTicketData['id'] =  $ticket->id;
                $tempTicketData['ticket_amount'] =number_format($ticketData->amount(), 2, '.', ',');
                $ticketsToSend[]  = $tempTicketData;
                
                $events[$key]['ticket_count']++;
            }

            DB::commit();

            $ticketIdsString = GeneratorUtils::generateStringWithDelimiter($ticketIds,':');
            $ticketIdsString = urlencode(base64_encode($ticketIdsString));
            $result_message =  'Transaction has been created successfully.';


            $ticketIds = base64_decode(urldecode($ticketIdsString));
            $ticketIds = explode(':',$ticketIds);

            $arr_result = [ 'message' => $result_message,
                        'outlet_name' => $outletName,
                        'transaction_number' => $transactionNumber,
                        'tickets' => array() ];

            for($pos=0; $pos<count($ticketIds); $pos++) {

              $ticket = Ticket::findOrFail($ticketIds[$pos]);
              $tData = $ticketsToSend[$pos];
              $outlet = $ticket->outlet()->first();

              $transc = $ticket->transaction()->first();

              $transactionData = new TransactionData($outlet, $transc);
              $ticketData = new \App\System\Data\Ticket($outlet, $ticket);
              $ticket_b64 = $this->getReceiptPrintingData($transactionData, $ticketData, $ticketIdsString);
              $arr_result['tickets'][] = [ 'id' => $ticketIds[$pos], 'data' => $tData ];
            }
            
            foreach ($events as $bet_schedule => $event) {
                event(new CalculatedSalesDataEvent($user, $event['amount'], $event['ticket_count'], $bet_schedule));
            }
            
            return response()->json($arr_result);
        } catch (QueryException $e) {
            DB::rollBack();
            $result_message =  'Error: QueryException.';
        } catch (FatalThrowableError $e) {
            DB::rollBack();
            $result_message =  'Error: '.$e;
        } catch (\Exception $e) {
            DB::rollBack();
            $result_message =  'Error: exception occurred. ' .$e;
        }
        return response()->json([ 'error' => $result_message]);
    }


    private function getReceiptPrintingData($transactionData,$ticketData,$tickets_str) {
        $html = View::make('outlets.print-api', array('transaction' => $transactionData,
            'ticket' => $ticketData,
            'tickets_str' => urlencode($tickets_str)))->render();

        $html = str_replace("<!DOCTYPE html>", "", $html);
        $html = preg_replace("/[\r\n]+/", " ", $html);

        $html = preg_replace("/[\s]/", " ", $html);


        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('outlets.print-api', array('transaction' => $transactionData,
            'ticket' => $ticketData,
            'tickets_str' => urlencode($tickets_str)))->setPaper('a4', 'portrait')->setWarnings(false);

        $b64Doc = chunk_split(base64_encode($pdf->stream()));
        $b64Doc = preg_replace("/[\r\n]+/", "", $b64Doc);
        $b64Doc = preg_replace("/[\s]/", "", $b64Doc);
        $b64Doc = str_replace(" ", "", $b64Doc);


        return $b64Doc;
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
            if (! in_array($bet['bet_schedule_input'], $datescheds)) {
                $datescheds[] = $bet['bet_schedule_input'];
            }
        }

        foreach ($datescheds as $dsched) {
            foreach ($bets as $bet) {
                if ($dsched === $bet['bet_schedule_input']) {
                    $tickets[$dsched][] = $bet;
                }
            }
        }

        return $tickets;
    }

    public function ajaxCalculateWinning()
    {
        $data = Input::all();
        $data = $data['data'];
        $json =  $data;
        $outlet_id = $data['outlet_id'];
        $outlet = Outlet::findOrfail($outlet_id);
        $game = GamesFactory::getGame($data['game'], $outlet);
        $errors = [];

        if (!$game->isValidBet($data['betNumber'])) {
            $errors[] = 'Invalid bet number';
        }

        if (!is_numeric($data['amount'])) {
            $errors[] = 'Invalid amount entered';
        }

        if (is_numeric($data['amount'])) {
            $amount = (int) $data['amount'];
            if ($amount > 500) {
                $errors[] = 'No bet shall exceed PHP 500.00.';
            }
        }

        if (!in_array($data['type'], $game->betTypes())) {
            $errors[] = 'Invalid bet type for ' . $game->label();
        }

        $schedTime = $data['sched_time'];

        $transactionService = new TransactionService($outlet);
        $isSoldOut = $transactionService->isSoldOut(
            $data['betNumber'],
            $data['amount'],
            $data['game'],
            $data['type'],
            date('Y-m-d'),
            TimeslotUtils::transactionInputStringToSchedKey($schedTime));
        
        if ($isSoldOut) {
            $errors[] = 'Your bet '.$data['betNumber'].' is already sold out. Please try another bet.';
        }


        if (count($errors) > 0) {
            $response = [
                'winning' => 0,
                'errors' => $errors,
            ];
        } else {
            $response = [
                'winning' => number_format($game->price(
                    $data['betNumber'],
                    $data['amount'],
                    $data['type']
                ), 2, '.', ','),
                'game' => GamesFactory::getGameLabelByGameName($data['game']),
            ];
        }

        return response()
            ->json($response);
    }

    private function captureInvalidTickets($data, $transaction_code = ""){
        if (!is_array($data)) {
            return;
        }

        if (!is_string($transaction_code)) {
            $transaction_code = "";
        }

        if (count($data)) {
            foreach ($data as $ticket) {
                try {
                    $ticketToSave = new InvalidTicket();
                    $ticketToSave->user_id =  $ticket['user_id'];
                    $ticketToSave->outlet_id =  $ticket['outlet_id'];
                    $ticketToSave->transaction_code = $transaction_code;
                    $ticketToSave->ticket_number =  $ticket['ticket_number'];
                    $ticketToSave->result_date =  $ticket['result_date'];
                    $ticketToSave->schedule_key =  $ticket['schedule_key'];
                    $ticketToSave->amount =  $ticket['ticket_amount'];
                    $ticketToSave->error =  $ticket['error'];
                    $ticketToSave->source =  $ticket['source'];
                    $ticketToSave->save();
                } catch (\Exception $e) {
                    
                }
            }
        }
    }

    private function logApiRequest($data) {
        if (is_array($data)) {
            $apiLog = new ApiLog();
            $json = json_encode($data);
            $apiLog->request = $json;
            $apiLog->save();
        }
    }

    public function getCancelledTransactions(Request $request) {

        $cancelledTxn = [];
        $isError = false;
        try {
            $start = $request->get('datefrom');
            $end = $request->get('dateto');

            if (TimeslotUtils::validateDate($start)  && TimeslotUtils::validateDate($end)) {
                $startCarbon = Carbon::createFromFormat('Y-m-d', $start, env('APP_TIMEZONE'));
                $endCarbon = Carbon::createFromFormat('Y-m-d', $end, env('APP_TIMEZONE'));
                if ($endCarbon->greaterThanOrEqualTo($startCarbon)) {
                    $request->merge([
                        'datefrom' => $request->get('datefrom'),
                        'dateto' => $request->get('dateto'),
                        ]);
                    $transactionCont = App::make('App\Http\Controllers\TransactionsController');
                    $cancelledTxn = $transactionCont->uniformTransactionsRetrievalInController('', 1, true, true);
                    if (array_key_exists("error", $cancelledTxn)) {
                        $cancelledTxn = [];
                        $isError = true;
                    }
                }
            }
        } catch (\Exception $e) {
            $isError = true;
        }
        return response()->json([ 'count' => count($cancelledTxn), 'cancelled_tickets' => $cancelledTxn, 'error' => $isError ]);
    }

    public function cancelTicket(Request $request) {
        $ticket_number = $request->get('ticket_number');
        $isSuccess = false;
        $message = "";
        try {
            $request->merge([
                'ticket_number' => $request->get('ticket_number'),
                ]);
            $dashboardCont = App::make('App\Http\Controllers\DashboardController');
            $results = $dashboardCont->postCancelTicket(true, true);
            $isSuccess = $results['success'];
            $message = $results['message'];
        } catch (\Exception $e) {
            $message = "An exception occured. Please contact the administrator. Error: " . $e->getMessage();
        }
        return response()->json([ 'success' => $isSuccess, 'message' => $message ]);
    }

    public function getTransactions(Request $request) {
        $userTxn = [];
        $isError = false;
        try {
            $start = $request->get('datefrom');
            $end = $request->get('dateto');

            if (TimeslotUtils::validateDate($start)  && TimeslotUtils::validateDate($end)) {
                $startCarbon = Carbon::createFromFormat('Y-m-d', $start, env('APP_TIMEZONE'));
                $endCarbon = Carbon::createFromFormat('Y-m-d', $end, env('APP_TIMEZONE'));
                if ($endCarbon->greaterThanOrEqualTo($startCarbon)) {
                    $request->merge([
                        'datefrom' => $request->get('datefrom'),
                        'dateto' => $request->get('dateto'),
                        ]);
                    $transactionCont = App::make('App\Http\Controllers\TransactionsController');
                    $userTxn = $transactionCont->uniformTransactionsRetrievalInController('', 1, false, true);
                    if (array_key_exists("error", $userTxn)) {
                        $userTxn = [];
                        $isError = true;
                    }
                }
            }
        } catch (\Exception $e) {
            $isError = true;
        }
        return response()->json([ 'count' => count($userTxn), 'tickets' => $userTxn, 'error' => $isError ]);
    }

    public function getInvalidAPITransactions(Request $request) {
        $invalidTxn = [];
        $isError = false;
        try {
            $start = $request->get('datefrom');
            $end = $request->get('dateto');

            if (TimeslotUtils::validateDate($start)  && TimeslotUtils::validateDate($end)) {
                $startCarbon = Carbon::createFromFormat('Y-m-d', $start, env('APP_TIMEZONE'));
                $endCarbon = Carbon::createFromFormat('Y-m-d', $end, env('APP_TIMEZONE'));
                if ($endCarbon->greaterThanOrEqualTo($startCarbon)) {
                    $reportsCont = App::make('App\Http\Controllers\ReportsController');
                    $invalidTxn = $reportsCont->invalidTickets($start, $end, true, true);
                }
            }
        } catch (\Exception $e) {
            $isError = true;
        }
        return response()->json([ 'count' => count($invalidTxn['invalid_tickets']), 'invalid_tickets' => $invalidTxn['invalid_tickets'], 'error' => $invalidTxn['error'] ]);
    }

    public function getWinningTickets(Request $request) {
        $isSuccess = true;
        $scheduleWinnings = [];
        $errMsg= "";
        try {
            $user = auth()->user();
            $draw_date = $request->get('draw_date');
            $sched = $request->get('draw_time'); // array
            $scheduleToCheck = [];
            if (!TimeslotUtils::validateDate($draw_date)) {
                throw new \Exception("Invalid date. Format should be YYYY-MM-DD");
            }
            if (is_array($sched)) {
              $arrKeys = array_intersect(Timeslot::drawTimeslots(), $sched);
              foreach ($arrKeys as $a) {
                $scheduleWinnings[$a] = [];
              }
            } else if ($sched != null) {
                $timeStr = "";
                foreach (Timeslot::drawTimeslots() as $time) {
                    $timeStr .= $time . ", ";
                }
                $timeStr = trim($timeStr, ", ");
                throw new \Exception("Invalid draw time. Draw time should be an array of strings with a subset of the following values: " . $timeStr);
            } else {
                foreach (Timeslot::drawTimeslots() as $time) {
                    $scheduleWinnings[$time] = [];
                }
            }
            $winningService = new WinningService();
            $winners = $winningService->getWinningBetsStored($draw_date, null, true);
            $winnersAggregated = $winningService->winnersAggregrateByDrawDateTime($winners);

            foreach ($winnersAggregated as $k => $winnersOnDrawTime) {
                list($date, $time) = explode(" ", $k);
                if (is_array($sched)) {
                    if (!in_array($time, $sched)) {
                        continue;
                    }
                }
                $currKey = Timeslot::getKeyByTime($time);
                
                foreach ($winnersOnDrawTime['winners'] as $winner) {
                    $scheduleWinnings[$time][] = [
                            "ticket_number" => $winner->ticketNumber(),
                            "outlet_name" => $winner->outletName(),
                            "teller" => $winner->teller(),
                            "customer" => $winner->customer(),
                            "bet" => $winner->bet(),
                            "game" => $winner->game(),
                            "type" => $winner->type(),
                            "amount" => $winner->amount(),
                            "prize" => $winner->winningPrize(),
                            "draw_datetime" => $k
                        ];
                }
            }    
        } catch (\Exception $e) {
            $isSuccess = false;
            $errMsg = $e->getMessage();
        }

        $output = [
            'tickets' => $scheduleWinnings,
            'success' => $isSuccess,
            'error' => $errMsg
        ];
        return response()->json($output);
    }

}
