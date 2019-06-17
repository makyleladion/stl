<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOutletRequest;
use App\Outlet;
use App\System\Services\OutletService;
use App\System\Utils\PaginationUtils;
use App\System\Utils\TimeslotUtils;
use App\User;
use App\System\Data\User as UserData;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use App\Http\Requests\EditOutletRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\System\Services\TransactionService;
use App\System\Data\Timeslot;
use App\System\Services\WinningService;
use App\HotnumberSearch;
use App\System\Games\Swertres\SwertresGame;
use App\System\Services\UserService;
use App\Events\CalculateWinningsEvent;
use App\InvalidTicket;
use Carbon\Carbon;
use App\System\Services\CachingService;

class ReportsController extends Controller
{
    private $cache;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->cache = new CachingService();
    }
    
    public function index()
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        return view('admin.reports.reports_summary');
    }

    public function summaryReports($page = '1', $draw_date = null)
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        if (!$draw_date || !TimeslotUtils::validateDate(urldecode($draw_date))) {
            $draw_date = date('Y-m-d');
        } else {
            $draw_date = urldecode($draw_date);
        }
        
        $origin = request()->query('origin', null);
        if (!array_key_exists($origin, \App\System\Data\Transaction::getTransactionOrigins())) {
            $origin = null;
        }
        
        $isUsher = false;
        if ($origin) {
            $isUsher = request()->query('is_usher', false);
            $isUsher = filter_var($isUsher, FILTER_VALIDATE_BOOLEAN);
        }

        try {
            $service = new OutletService();

            $query = Outlet::query();
            $totalCount = $this->attachAllowedIds($query)->count();
            $resultsPerPage = PaginationUtils::globalRecordsPerPage();
            $offsetLimit = PaginationUtils::getOffsetLimitByPageNumber($totalCount, $resultsPerPage, (int) $page);
            $totalPages = PaginationUtils::calculateNumberOfPages($totalCount, $resultsPerPage);
            
            $outletsSummary = [];
            $usersSummary = [];
            
            if ($origin) {
                
                if ($isUsher) {
                    $userService = new UserService();
                    $usersSummary = $userService->getUsersSummary($offsetLimit['offset'], $resultsPerPage, $draw_date, OutletService::OUTLETS_SORT_TOTAL, $origin, $isUsher);
                } else {
                    $outletsSummary = $service->getOutletsSummary($offsetLimit['offset'], $resultsPerPage, $draw_date, OutletService::OUTLETS_SORT_TOTAL, $origin, $isUsher);
                }
            } else {
                $outletsSummary = $service->getOutletsSummary($offsetLimit['offset'], $resultsPerPage, $draw_date);
            }
            
            $totals = [];
            foreach ($outletsSummary as $summary) {
                foreach (Timeslot::getTimeslotKeys() as $schedKey) {
                    if (!isset($totals[$schedKey])) {
                        $totals[$schedKey] = 0;
                    }
                    $totals[$schedKey] = bcadd($totals[$schedKey], $summary['sales'][$schedKey]);
                }
            }
            foreach ($usersSummary as $summary) {
                foreach (Timeslot::getTimeslotKeys() as $schedKey) {
                    if (!isset($totals[$schedKey])) {
                        $totals[$schedKey] = 0;
                    }
                    $totals[$schedKey] = bcadd($totals[$schedKey], $summary['sales'][$schedKey]);
                }
            }
            $totals['overall'] = 0;
            foreach (Timeslot::getTimeslotKeys() as $schedKey) {
                if (array_key_exists($schedKey, $totals)) {
                    $totals['overall'] = bcadd($totals['overall'], $totals[$schedKey]);
                } else {
                    $totals[$schedKey] = 0;
                }
            }
            
            return view('admin.reports.reports_summary', [
                'draw_date' => $draw_date,
                'total_outlets' => $totalCount,
                'results_per_page' => $resultsPerPage,
                'total_pages' => $totalPages,
                'outlets_summary' => $outletsSummary,
                'users_summary' => $usersSummary,
                'page' => $page,
                'prev' => PaginationUtils::getPreviousPageNumber($page, $totalCount, $resultsPerPage),
                'next' => PaginationUtils::getNextPageNumber($page, $totalCount, $resultsPerPage),
                'origin' => \App\System\Data\Transaction::getTransactionOrigins(),
                'current_origin' => $origin,
                'totals' => $totals,
                'is_usher' => $isUsher,
            ]);
        } catch (QueryException $e) {
            report($e);
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            report($e);
            abort(500,$e->getMessage());
        }
    }
    
    public function highestBets($draw_date = null)
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        if (!$draw_date || !TimeslotUtils::validateDate(urldecode($draw_date))) {
            $draw_date = date('Y-m-d');
        } else {
            $draw_date = urldecode($draw_date);
        }
        
        try {
            $service = new WinningService();
            $highestWinnings = $service->getHighestWinningBets($draw_date);
            
            return view('admin.reports.highest_bet', [
                'draw_date' => $draw_date,
                'highest_winnings' => $highestWinnings,
            ]);
        } catch (QueryException $e) {
            report($e);
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            report($e);
            abort(500,$e->getMessage());
        }
    }
    
    public function hotNumbers($top = '2000', $game = null, $draw_date = null, $sched_key = null)
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        if (empty($game)) {
            $game = SwertresGame::name();
        }
        
        if (!$draw_date || !TimeslotUtils::validateDate(urldecode($draw_date))) {
            $draw_date = date('Y-m-d');
        } else {
            $draw_date = urldecode($draw_date);
        }
        
        try {
            $service = new WinningService();
            $hotNumbers = $service->getHotNumbers($draw_date, 0, (int) $top, $game, $sched_key);
            
            return view('admin.reports.hotnumbers', [
                'draw_date' => $draw_date,
                'hotnumbers' => $hotNumbers,
                'top' => $top,
                'game' => $game,
                'sched_key' => urldecode($sched_key),
                'hot_numbers_json' => json_encode($hotNumbers),
            ]);
        } catch (QueryException $e) {
            report($e);
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            report($e);
            abort(500,$e->getMessage());
        }
    }
    
    public function ajaxRecordSearch()
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        try {
            
            $date     = \request()->input('result_date');
            $schedKey = \request()->input('schedule_key', '');
            $keyword  = \request()->input('keyword');
            
            HotnumberSearch::create([
                'user_id' => auth()->user()->id,
                'result_date' => $date,
                'schedule_key' => $schedKey,
                'keyword' => $keyword,
            ]);
            
            return response()->json([
                'user_id' => auth()->user()->id,
                'result_date' => $date,
                'schedule_key' => $schedKey,
                'keyword' => $keyword,
            ]);
        } catch (QueryException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            abort(500,$e->getMessage());
        }
    }
    
    public function dateRangeCalculations($page = '1', $date_from = null, $date_to = null)
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        if (!$date_from || !TimeslotUtils::validateDate(urldecode($date_from))) {
            $date_from = date('Y-m-d');
        } else {
            $date_from = urldecode($date_from);
        }
        
        if (!$date_to || !TimeslotUtils::validateDate(urldecode($date_to))) {
            $date_to = date('Y-m-d');
        } else {
            $date_to = urldecode($date_to);
        }
        
        $origin = request()->query('origin', null);
        if (!array_key_exists($origin, \App\System\Data\Transaction::getTransactionOrigins())) {
            $origin = null;
        }
        
        $isUsher = false;
        if ($origin) {
            $isUsher = request()->query('is_usher', false);
            $isUsher = filter_var($isUsher, FILTER_VALIDATE_BOOLEAN);
        }
        
        try {
            $service = new OutletService();
            
            $query = Outlet::query();
            
            $totalCount = $this->attachAllowedIds($query)->count();
            $resultsPerPage = PaginationUtils::globalRecordsPerPage();
            $offsetLimit = PaginationUtils::getOffsetLimitByPageNumber($totalCount, $resultsPerPage, (int) $page);
            $totalPages = PaginationUtils::calculateNumberOfPages($totalCount, $resultsPerPage);
            
            if ($origin) {
                $outletsSummary = $service->getOutletsSummaryDateRange($offsetLimit['offset'], $resultsPerPage, $date_from, $date_to, OutletService::OUTLETS_SORT_TOTAL, $origin, $isUsher);
            } else {
                $outletsSummary = $service->getOutletsSummaryDateRange($offsetLimit['offset'], $resultsPerPage, $date_from, $date_to);
            }
            
            $totals = [];
            foreach ($outletsSummary as $summary) {
                foreach (Timeslot::getTimeslotKeys() as $schedKey) {
                    if (!isset($totals[$schedKey])) {
                        $totals[$schedKey] = 0;
                    }
                    $totals[$schedKey] = bcadd($totals[$schedKey], $summary['sales'][$schedKey]);
                }
            }
            $totals['overall'] = 0;
            foreach (Timeslot::getTimeslotKeys() as $schedKey) {
                if (array_key_exists($schedKey, $totals)) {
                    $totals['overall'] = bcadd($totals['overall'], $totals[$schedKey]);
                } else {
                    $totals[$schedKey] = 0;
                }
            }
            
            return view('admin.reports.date_range_calculations', [
                'date_from' => $date_from,
                'date_to' => $date_to,
                'total_outlets' => $totalCount,
                'results_per_page' => $resultsPerPage,
                'total_pages' => $totalPages,
                'outlets_summary' => $outletsSummary,
                'page' => $page,
                'prev' => PaginationUtils::getPreviousPageNumber($page, $totalCount, $resultsPerPage),
                'next' => PaginationUtils::getNextPageNumber($page, $totalCount, $resultsPerPage),
                'origin' => \App\System\Data\Transaction::getTransactionOrigins(),
                'current_origin' => $origin,
                'totals' => $totals,
                'is_usher' => $isUsher,
            ]);
        } catch (QueryException $e) {
            report($e);
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            report($e);
            abort(500,$e->getMessage());
        }
    }
    
    public function dateRangeCalculationsWinnings()
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        $date_from = date('Y-m-d');
        $date_to = date('Y-m-d');
        
        return view('admin.reports.date_range_calculations_winnings', [
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }
    
    public function triggerRangeCalculationsWinnings($date_from = null, $date_to = null)
    {
        try {
            if (!TimeslotUtils::validateDate($date_from)) {
                throw new \Exception('Please enter a valid date on date_from.');
            }
            if (!TimeslotUtils::validateDate($date_to)) {
                throw new \Exception('Please enter a valid date on date_to.');
            }
            
            event(new CalculateWinningsEvent($date_from, $date_to));
            
            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function invalidTickets($start = "", $end = "", $filterByUser = false, $returnDataOnly = false) {
        if (!auth()->user()->is_admin && !$returnDataOnly) {
            abort(404, 'Only privileged users are allowed.');
        }

        if ($start == "" && $end == "") {
            $start = TimeslotUtils::dateToday();
            $end = TimeslotUtils::dateToday();
        }
        
        $isSuperAdmin = false;
        $hasError = false;

        try {
            $invalidTicketsParsed = [];
            $user = auth()->user();
            $outlets = $user->outlets()->get()->toArray();
            $outlet_id = [];
            $outlet_details = [];
            
            if (!$outlets) {
                $outlets = Outlet::get(['id','name'])->toArray();
                $isSuperAdmin = true;
            }

            if ($outlets) {
                foreach($outlets as $outlet) {
                    $outlet_id[] = $outlet['id'];
                    $outlet_details[$outlet['id']] = [ 
                            "name" => $outlet['name']
                        ];
                }
            } 
            if (TimeslotUtils::validateDate($start)  && TimeslotUtils::validateDate($end)) {
                $invalidTickets = InvalidTicket::where('created_at', ">=", $start)->where('created_at', "<=",  $end . " 23:59:59");

                if ($outlet_id > 0 && !$isSuperAdmin) {
                    $invalidTicketsResults = $invalidTickets->whereIn("outlet_id", $outlet_id)->where("user_id", $user->id )->orderBy('created_at', 'desc')->get();
                } else {
                    $invalidTicketsResults = $invalidTickets->orderBy('created_at', 'desc')->get();
                }
                if ($invalidTickets) {
                    foreach ($invalidTicketsResults as $invalidTicket) {
                        try {
                            $dt = Carbon::createFromFormat('Y-m-d', $invalidTicket->result_date, env('APP_TIMEZONE'));
                            $dateString = $dt->toFormattedDateString(); 
                        } catch (\Exception $e)  {
                            $dateString = "$invalidTicket->result_date";
                        }
                        try {
                            $time = Timeslot::getTimeByKey($invalidTicket->schedule_key);
                            $tempDateTime = $invalidTicket->result_date . " " . $time;
                            $dt = Carbon::createFromFormat('Y-m-d H:i:s', $tempDateTime, env('APP_TIMEZONE'));
                            $dateString = $dt->toDayDateTimeString();
                            
                        } catch (\Exception $e) {
                            $time = "";
                        }
                        
                        try {
                            $dt = Carbon::createFromFormat('Y-m-d H:i:s', $invalidTicket->created_at, env('APP_TIMEZONE'));
                            $created_at = $dt->toDayDateTimeString();
                        } catch(\Exception $e) {
                            $created_at = "UNKNOWN";
                        }
                        

                        $invalidTicketFormatted = [
                            'created_at' => $created_at,
                            'transaction_code' => $invalidTicket->transaction_code,
                            'ticket_number' => $invalidTicket->ticket_number,
                            'outlet_id' => $invalidTicket->outlet_id,
                            'outlet' => isset($outlet_details[$invalidTicket->outlet_id]) ? $outlet_details[$invalidTicket->outlet_id] : "UNKNOWN",
                            'result_date' => $dateString,
                            'schedule_key' => $invalidTicket->schedule_key,
                            'amount' => $invalidTicket->amount,
                            'error' => $invalidTicket->error,
                            'source' => is_numeric(strpos($invalidTicket->source, "mobile")) ? "Mobile" : "Outlet",
                            'customer' => '',
                            'user' => null
                        ];
                        if ($invalidTicket) {
                            $tellerForInvalidTicket = $invalidTicket->user()->first();
                            if ($tellerForInvalidTicket) {
                                $tellerForInvalidTicket = $tellerForInvalidTicket->toArray();

                                $invalidTicketFormatted['user'] = [
                                    "id" => $tellerForInvalidTicket['id'],
                                    "name" => $tellerForInvalidTicket['name'],
                                ];
                                
                            }
                        }
                        $invalidTicketsParsed[] = $invalidTicketFormatted;
                    }
                }
            } else {
                Session::flash('error-flash', 'Invalid dates');
                $hasError = true;
            }
        } catch (\Exception $e) {
            if (!$returnDataOnly) {
                abort(404, 'Something went wrong.');
            } else {
                $hasError = true;
            }
        }

        if ($returnDataOnly) {
            return [
                "invalid_tickets" => $invalidTicketsParsed, 
                "error" => $hasError
            ];
        }

        return view('admin.reports.transactions-invalid', [
            'invalid_tickets' => $invalidTicketsParsed,
            'date_from' => $start,
            'date_to' => $end
        ]);
    }
    
    private function getAllowedIds()
    {
        $user = new UserData(auth()->user());
        
        $allowedId = [];
        $allowedId[] = $user->id();
        foreach ($this->cache->getUserLeaves($user) as $sub) {
            $allowedId[] = $sub->id;
        }
        
        return $allowedId;
    }
    
    private function attachAllowedIds($query)
    {
        $allowedIds = $this->getAllowedIds();
        return $query->whereHas('tellers.user', function($q) use ($allowedIds) {
            $q->whereIn('id', $allowedIds);
        });
    }
}
