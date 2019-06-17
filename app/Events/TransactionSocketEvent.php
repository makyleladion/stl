<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Outlet;
use App\Transaction;

class TransactionSocketEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $transaction;
    
    public $user_id;
    
    public $id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Outlet $outlet, Transaction $transaction)
    {
        $transactionObject = new \App\System\Data\Transaction($outlet, $transaction);
        $this->transaction = $this->renderHTMLString($transactionObject);
        $this->user_id = $transaction->user_id;
        $this->id = $transactionObject->id(true);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('transactions-broadcast');
    }
    
    public function broadcastAs()
    {
        return 'transactions-broadcast.per-transaction';
    }
    
    private function renderHTMLString(\App\System\Data\Transaction $transaction)
    {
        $html = '<tr class="table-success" id="transaction-'.$transaction->id(true).'">';
        $html .= '<td>'.$transaction->id(true).'</td>';
        $html .= '<td>'.$transaction->transactionNumber().'</td>';
        $html .= '<td>'.$transaction->tickets(true).'</td>';
        $html .= '<td><a href="'.route('outlet-dashboard', ['outlet_id' => $transaction->getOutlet()->id]).'">'.$transaction->outletName().'</a></td>';
        $html .= '<td><a href="'.route('edit-user', ['user_id' => $transaction->tellerObj()->id]).'">'.$transaction->teller().'</a></td>';
        $html .= '<td>'.$transaction->getBetGameLabels(true).'</td>';
        $html .= '<td>'.$transaction->betsString().'</td>';
        $html .= '<td>'.$transaction->numberOfBets().'</td>';
        $html .= '<td>PHP '.number_format($transaction->amount(), 2, '.', ',').'</td>';
        $html .= '<td>'.$transaction->transactionDateTime()->toDayDateTimeString().'</td>';
        $html .= '<td>'.$transaction->getDrawDateTimes().'</td>';
        $html .= '<td>';
        $html .= '<a href="'.route('single-transaction', ['transaction_id' => $transaction->getTransaction()->id, 'outlet_id' => $transaction->getOutlet()->id]).'" class="btn btn-default btn-sm">View</a>';
        $html .= '</td>';
        $html .= '</tr>';
        
        return $html;
    }
}
