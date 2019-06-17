<?php

namespace App\Http\Controllers;

use App\System\Data\Transaction;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Cookie\CookieJar;
use Illuminate\Support\Facades\View;

class PrintController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display printable receipt.
     *
     * @param $ticket_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function receipt($ticket_id)
    {
        $ticket = Ticket::findOrFail($ticket_id);
        $outlet = $ticket->outlet()->first();
        $transc = $ticket->transaction()->first();
        
        $count = (int) $ticket->print_count;
        $ticket->print_count = ++$count;
        $ticket->save();

        $transactionData = new Transaction($outlet, $transc);
        $ticketData = new \App\System\Data\Ticket($outlet, $ticket);

        return view('outlets.print', [
            'transaction' => $transactionData,
            'ticket' => $ticketData,
        ]);
    }

    /**
     * Display printable receipts under a transaction.
     *
     * @param $tickets_str
     * @param $pos
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function multipleReceipts(CookieJar $cookie, $tickets_str, $pos)
    {
        $ticketIds = base64_decode(urldecode($tickets_str));
        $ticketIds = explode(':',$ticketIds);
        if ($pos < 0 && $pos >= count($ticketIds)) {
            throw new \Exception('Current ticket array index position is not valid');
        }

        $ticket = Ticket::findOrFail($ticketIds[$pos]);
        $outlet = $ticket->outlet()->first();
        $transc = $ticket->transaction()->first();
        
        $count = (int) $ticket->print_count;
        $ticket->print_count = ++$count;
        $ticket->save();

        $transactionData = new Transaction($outlet, $transc);
        $ticketData = new \App\System\Data\Ticket($outlet, $ticket);
        $next_page = $pos + 1;

        $cookie->queue('current_print_tickets_str', $tickets_str, 45000);

        return view('outlets.print', [
            'transaction' => $transactionData,
            'ticket' => $ticketData,
            'tickets_str' => urlencode($tickets_str),
            'next_page' => ($next_page < count($ticketIds)) ? $next_page : false,
        ]);
        
        //return $this->qzReceiptPrinting($transactionData,$ticketData,$tickets_str,$ticketIds,$next_page);
    }

    /**
     * Display printable sales receipts under a transaction.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function salesPrintReceipts($pos)
    {
        return view('outlets.sales-print');
    }

    private function qzReceiptPrinting($transactionData,$ticketData,$tickets_str, $ticketIds,$next_page) {
        $html = View::make('outlets.print-qz', array('transaction' => $transactionData,
            'ticket' => $ticketData,
            'tickets_str' => urlencode($tickets_str)))->render();

        $html = str_replace("<!DOCTYPE html>", "", $html);
        $html = preg_replace("/[\r\n]+/", " ", $html);

        $html = preg_replace("/[\s]/", " ", $html);
        return view('outlets.qzprint', ['data' => $html,
            'transaction' => $transactionData,
            'ticket' => $ticketData,
            'tickets_str' => urlencode($tickets_str),
            'next_page' => ($next_page < count($ticketIds)) ? $next_page : false,
        ]);
    }
}
