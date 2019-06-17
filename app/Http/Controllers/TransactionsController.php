<?php

namespace App\Http\Controllers;

use App\Outlet;
use App\System\Data\Transaction as TransactionData;
use App\System\Services\OutletService;
use App\System\Services\TransactionService;
use App\System\Utils\PaginationUtils;
use App\Transaction;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\System\Services\TransactionFilter;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\System\Utils\EncryptionUtils;
use App\System\Utils\GeneratorUtils;

use App\Ticket;
use App\Bet;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use App\System\Utils\UserUtils;
use App\System\Data\User;

class TransactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show all transactions from all outlets.
     *
     * @param string $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function all()
    {
        $page = request()->query('page', 1);
        return $this->uniformTransactionsRetrievalInController('admin.transactions', $page);
    }
    
    public function allCanceled($page = '1')
    {
        return $this->uniformTransactionsRetrievalInController('admin.transactions-canceled', $page, true);
    }

    public function perOutlet($outlet_id, $page = '1')
    {
        return 'Per Outlet Transactions';
    }

    /**
     * Show a single transaction.
     *
     * @param $outlet_id
     * @param $transaction_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function single($outlet_id, $transaction_id)
    {
        $outlet = Outlet::findOrFail($outlet_id);
        $transaction = $outlet->Transactions()->findOrFail($transaction_id);
        $service = new TransactionService($outlet);

        return view('outlets.transaction', [
            'transaction' => new TransactionData($outlet, $transaction),
            'transaction_array' => $service->getTransactionDataAsArray($transaction_id),
        ]);
    }
    
    public function pdfDownload($page = '1')
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        try {
            $filter = new TransactionFilter();
            
            try {
                // Date Range filter
                $datefrom = \request()->query('datefrom');
                $dateto = \request()->query('dateto');
                if ($datefrom) {
                    $filter->setDateRange($datefrom, $dateto);
                }
                
                // Specific Outlet filter
                $specificOutletId = \request()->query('outlet');
                if ($specificOutletId && is_numeric($specificOutletId)) {
                    $filter->setSpecificOutletById($specificOutletId);
                }
                
                // Specific Teller filter
                $specificTellerId = \request()->query('teller');
                if ($specificTellerId && is_numeric($specificTellerId)) {
                    $filter->setSpecificTellerById($specificTellerId);
                }
            } catch (\Exception $e) {
                abort(404, $e->getMessage());
            }
            
            $url = parse_url(\request()->fullUrl());
            $urlQuery = isset($url['query']) ? $url['query'] : null;
            
            $service =  new TransactionService();
            
            $totalCountQuery = Transaction::query();
            $totalCount = $filter->execute($totalCountQuery)->count();
            
            $resultsPerPage = PaginationUtils::globalRecordsPerPage();
            $offsetLimit = PaginationUtils::getOffsetLimitByPageNumber($totalCount, $resultsPerPage, (int) $page);
            $transactions = $service->getTransactions($offsetLimit['offset'], $resultsPerPage, $filter);
            
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadView('admin.pdf', [
                'page' => $page,
                'query' => $urlQuery,
                'transactions' => $transactions,
            ])->setPaper('a4', 'landscape')->setWarnings(false);
            
            $startTime = (isset($transactions[0])) ? $transactions[0]->transactionDateTime()->timestamp : Carbon::now()->timestamp;
            $filename = sprintf("transactions-%s.pdf", $startTime);
            return $pdf->save('pdf/' . $filename)->download($filename);
            
        } catch (QueryException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }
    
    public function uniformTransactionsRetrievalInController($view, $page, $isCanceled = false, $fromMobile = false)
    {
        if (!auth()->user()->is_admin && !$fromMobile) {
            abort(404, 'Only privileged users are allowed.');
        }

        try {
            $filter = new TransactionFilter();
            $buildQuery = '';
            
            try {
                // Date Range filter
                $datefrom = $fromMobile ?  Input::get('datefrom') : \request()->query('datefrom');
                $dateto = $fromMobile ?  Input::get('dateto') : \request()->query('dateto');
                if ($datefrom) {
                    $filter->setDateRange($datefrom, $dateto);
                    $buildQuery = $this->appendURLQuery($buildQuery, 'datefrom', $datefrom);
                    if ($dateto) {
                        $buildQuery = $this->appendURLQuery($buildQuery, 'dateto', $dateto);
                    }
                }
                
                // Specific Outlet filter
                $specificOutletId = \request()->query('outlet');
                if ($specificOutletId && is_numeric($specificOutletId)) {
                    $filter->setSpecificOutletById($specificOutletId);
                    $buildQuery = $this->appendURLQuery($buildQuery, 'outlet', $specificOutletId);
                }
                
                // Specific Teller filter
                $specificTellerId = $fromMobile ? auth()->user()->id : \request()->query('teller');
                if ($specificTellerId && is_numeric($specificTellerId)) {
                    $filter->setSpecificTellerById($specificTellerId);
                    $buildQuery = $this->appendURLQuery($buildQuery, 'teller', $specificTellerId);
                }
                
                if ($isCanceled) {
                    $filter->setIsCanceled($isCanceled);
                }
            } catch (\Exception $e) {
                Session::flash('error-flash', $e->getMessage());
            }
            
            $url = parse_url(\request()->fullUrl());
            $urlQuery = isset($url['query']) ? $url['query'] : null;
            
            $outlets = Outlet::where('status', '!=', \App\System\Data\Outlet::STATUS_CLOSED)->orderBy('name', 'asc')->get();
            
            $service =  new TransactionService();
            
            $totalCountQuery = Transaction::query();
            $allowedIds = UserUtils::leavesIds(auth()->user(),$fromMobile);
            $totalCountQuery->whereHas('user', function($q) use ($allowedIds) {
                if ($allowedIds) {
                    $q->whereIn('id', $allowedIds);
                }
                
            });
            $totalCount = $filter->execute($totalCountQuery)->count();
            
            $resultsPerPage = $fromMobile ? $totalCount : PaginationUtils::globalRecordsPerPage();
            $offsetLimit = PaginationUtils::getOffsetLimitByPageNumber($totalCount, $resultsPerPage, (int) $page);
            $transactions = $service->getTransactions($offsetLimit['offset'], $resultsPerPage, $filter, $fromMobile);
            $totalPages = PaginationUtils::calculateNumberOfPages($totalCount, $resultsPerPage);

            if ($fromMobile) {
                $parsedTransactions = [];
                try {
                    foreach($transactions['transactions'] as $aTxn) {
                        $cancelledDateStr = $aTxn->cancelDates();
                        $parsedTransaction = [
                            "transaction_number"  => $aTxn->transactionNumber(),
                            "amount" => $aTxn->amount($isCanceled),
                            "cancelled_dates" => $cancelledDateStr,
                            "has_cancelled" => !empty($cancelledDateStr),
                            "cancelled_by" => $aTxn->canceledBy(),
                        ];
    
                        $parsedTransactionTickets = [];
                        foreach ($aTxn->tickets() as $aTxnTicket) {
    
                            $parsedTransactionBets = [];
    
                            foreach ($aTxnTicket->bets() as $aTxnBet) {
                                $parsedTransactionBet = [
                                    "bet_number" => $aTxnBet->betNumber(),
                                    "bet_type" => $aTxnBet->betTypeAbbreviation(),
                                    "bet_amount" => $aTxnBet->amount(),
                                    "bet_game" => $aTxnBet->gameLabel(),
                                    "bet_price" => $aTxnBet->price(),
                                ];
    
                                $parsedTransactionBets[] = $parsedTransactionBet;
                            }
    
                            $parsedTransactionTicket = [
                                "ticket_number" => $aTxnTicket->ticketNumber(),
                                "date_created" => $aTxnTicket->betDateTime()->format('Y-m-d H:i:s'),
                                "draw_datetime" => $aTxnTicket->drawDateTime(),
                                "cancellation_date" => is_object($aTxnTicket->cancellationDetails()) ? $aTxnTicket->cancellationDetails()->created_at->format('Y-m-d H:i:s') : "",
                                "bets" => $parsedTransactionBets,
                            ];
                            $parsedTransactionTicket['is_cancelled'] = !empty($parsedTransactionTicket['cancellation_date']);

                            $parsedTransactionTickets[] = $parsedTransactionTicket;
                        }
                        $parsedTransaction['tickets'] = $parsedTransactionTickets;
    
                        $parsedTransactions[] = $parsedTransaction;
                    }
                } catch(\Exception $e) {
                    $parsedTransactions['error'] = true;
                }
                return $parsedTransactions;
                
            }
            
            $userData = new User(auth()->user());
            $allowedIds = [];
            if (!$userData->isSuperAdmin()) {
                $allowedIds = UserUtils::leavesIds($userData);
            }
            
            return view($view, [
                'total_transactions' => $totalCount,
                'results_per_page' => $resultsPerPage,
                'transactions' => $transactions['transactions'],
                'raw_transactions' => $transactions['raw_transactions'],
                'total_pages' => $totalPages,
                'page' => $page,
                'prev' => PaginationUtils::getPreviousPageNumber($page, $totalCount, $resultsPerPage),
                'next' => PaginationUtils::getNextPageNumber($page, $totalCount, $resultsPerPage),
                'query' => $buildQuery,
                'outlets' => $outlets,
                'allowed_ids' => $allowedIds,
                'is_superadmin' => $userData->isSuperAdmin(),
            ]);
        } catch (QueryException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }
    
    public function exportUnsyncTransactions() {
        
        $exportJson = "{\"data\":[";
        
        $now = Carbon::now(env('APP_TIMEZONE'));
        $transactions = Transaction::where('sync','=','0')->whereDate('created_at', $now->toDateString())->get()->toArray();
        
        $size = count($transactions);
        $count = 0;
        foreach ($transactions as $transaction) {
            $payload = $transaction;
            $payload['transaction_id'] = $transaction['id'];
            $payload['tickets'] = array();
            $ticketsResults = Ticket::where('transaction_id','=',$transaction['id'])->get()->toArray();
            
            foreach ($ticketsResults as $ticket) {
                $ticketToAdd = $ticket;
		            $ticketToAdd['bets'] = array();
                $betResults = Bet::where('transaction_id','=',$transaction['id'])->get()->toArray();
                foreach ($betResults as $bet) {
                    $ticketToAdd['bets'][] = $bet;
                }
		            $payload['tickets'][] = $ticketToAdd;
            }
            $count++;
            
            $postFix = ($count<$size) ? "," : "";
            
            $exportJson .= json_encode($payload).$postFix;
        }
        
        $exportJson .= "]}";
        if (!empty($exportJson)) {
            $encryptor = new EncryptionUtils();
            $encryptedData = $encryptor->encryptSpecial($exportJson);
            
            $fileName = storage_path()."/app/". time() . '_transactions_unsync.json';
            File::put($fileName,$encryptedData);
            return Response::download($fileName);
        }
        
        return false;
    }
    
    public function importTransactions() {
        $importedCount = 0;
        if (Input::hasFile('transactionimport')) {
            $path = Input::file('transactionimport')->getRealPath();
            if (File::exists($path)) {
                $filecontent = file_get_contents($path);
                $encryptor = new EncryptionUtils();
                $decryptedData = $encryptor->decryptSpecial($filecontent);
                $jsonDecode = (array) json_decode($decryptedData);
                if ($jsonDecode) {
                    $transactions = $jsonDecode['data'];
                    if (is_array($transactions)) {
                        foreach ($transactions as $data) {
                            $data = (array) $data;
                            // Transaction
                            $outlet_id = $data['outlet_id'];
                            $user_id = $data['user_id'];
                            $transaction_code = $data['transaction_code'];
                            $customer_name = $data['customer_name'];
                            $created_at = $data['created_at'];
                            $updated_at = $data['updated_at'];

                            // Insert transaction
                            $tx = new Transaction();
                            $tx->user_id = $user_id;
                            $tx->outlet_id = $outlet_id;
                            $tx->transaction_code = $transaction_code; // Possible duplicate of transaction code will produce mySQL integrity constraint violation
                            $tx->customer_name = $customer_name;
                            $tx->created_at = $created_at;
                            $tx->updated_at = $updated_at;
                            $tx->save();
                            $transaction_id = $tx->id;

                            // Ticket
                            $tickets = $data['tickets']; // $tickets not sure if array
                            foreach ($tickets as $ticket) {
                                $ticket = (array) $ticket;
                                $ticketToSave = new Ticket();
                                $ticketToSave->user_id = $user_id;
                                $ticketToSave->outlet_id = $outlet_id;
                                $ticketToSave->transaction_id = $transaction_id;
                                $ticketToSave->ticket_number = $ticket['ticket_number'];
                                $ticketToSave->result_date = $ticket['result_date'];
                                $ticketToSave->schedule_key = $ticket['schedule_key'];
                                $ticketToSave->is_cancelled = $ticket['is_cancelled'];
                                $ticketToSave->created_at = $ticket['created_at'];
                                $ticketToSave->updated_at = $ticket['updated_at'];
                                $ticketToSave->save();
                                $ticket_id = $ticketToSave->id;

                                // Bets
                                $bets = $ticket['bets']; // $bets
                                foreach ($bets as $bet) {
                                    $bet = (array) $bet;
                                    $betToSave = new Bet();
                                    $betToSave->outlet_id = $outlet_id;
                                    $betToSave->transaction_id = $transaction_id;
                                    $betToSave->ticket_id = $ticket_id;
                                    $betToSave->amount = $bet['amount'];
                                    $betToSave->type = $bet['type'];
                                    $betToSave->number = $bet['number'];
                                    $betToSave->game = $bet['game'];
                                    $betToSave->created_at = $bet['created_at'];
                                    $betToSave->updated_at = $bet['updated_at'];
                                    $betToSave->save();
                                }
                            }
                            $importedCount++;
                        }
                    }
                }
            }
        }
        
        return redirect('dashboard')->with('message_import', 'Import done. Total imported: '. $importedCount);
    }
    
    private function appendURLQuery($buildQuery, $name, $append)
    {
        $buildQuery .= (empty($buildQuery)) ? '' : '&';
        $buildQuery .= $name . '=' . $append;
        
        return $buildQuery;
    }
}
