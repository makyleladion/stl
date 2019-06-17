<?php
namespace App\System\Services;

use App\Bet;
use App\OfflineSyncLog;
use App\Ticket;
use App\Transaction;
use Carbon\Carbon;

class OfflineSyncService
{
    public function transactionSync($token, $url)
    {
        $new_transaction_url = $url."/new_transaction";
        $ping_server_url = $url."/ping_server";
        
        if (!is_numeric(strpos($ping_server_url, 'api_token'))) {
            $ping_server_url .= "?api_token=$token";
        }
        
        if (!is_numeric(strpos($new_transaction_url, 'api_token'))) {
            $new_transaction_url .= "?api_token=$token";
        }
        
        
        $header = array("Content-Type: application/json", "authorization: Bearer $token");
        $response = $this->curlAPI($ping_server_url, "", $header, "GET");
        
        if (!is_object($response)) {
            $msg_log = "Server not online or wrong URL/token. Sync postponed.";
            $this->generateOfflineSyncLog($msg_log);
            return 0;
        }
        
        $now = Carbon::now(env('APP_TIMEZONE'));
        $transactions = Transaction::where('sync','=','0')->whereDate('created_at', $now->toDateString())->get()->toArray();
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
            
            $data = json_encode($payload);
            $response = $this->curlAPI($new_transaction_url, $data, $header, "POST");
            if ($response!=null) { // TODO confirm if response null is the fail indicator
                $transUpdate = Transaction::where('id','=',$transaction['id'])->firstOrFail();
                if ($transUpdate) {
                    $transUpdate->sync = 1;
                    $transUpdate->save();
                }
                $msg_log = "Successfully synced transaction ".$transaction['id'];
                $this->generateOfflineSyncLog($msg_log);
            } else  {
                $msg_log = "Failed syncing transaction ".$transaction['id'];
                $this->generateOfflineSyncLog($msg_log);
            }
        }
    }
    
    private function curlAPI($url, $data, $header, $request_type = "POST")
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        if ($request_type == "POST") {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        } else if ($request_type == "GET") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request_type);
            curl_setopt($curl, CURLOPT_HTTPGET, 1);
            curl_setopt($curl, CURLOPT_POST, 0);
        } else if ($request_type == "PUT") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request_type);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        } else if ($request_type == "DELETE") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request_type);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        if (strpos($url, 'https') !== false) {
            curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        } else {
            curl_setopt($curl, CURLOPT_SSLVERSION, 1);
        }
        
        $json_response = curl_exec($curl);
        
        if (false === $json_response) {
            $error = new \stdClass();
            $error->message = curl_error($curl);
            $error->error = curl_errno($curl);
            $error->url = $url;
            $error->data = json_decode($data);
            $error->header = $header;
            $error->request_type = $request_type;
            $error->response = json_decode($json_response);
            
            curl_close($curl);
            self::generateOfflineSyncLog("cURL error: " . $error->message);
            return null;
        }
        
        curl_close($curl);
        return json_decode($json_response);
    }
    
    
    private function generateOfflineSyncLog($msg = "")
    {
        $osl = new OfflineSyncLog();
        $osl->transaction_id = 0;
        $date = Carbon::now();
        $dt = Carbon::createFromFormat('Y-m-d H:i:s', $date, env('APP_TIMEZONE'));
        $osl->sync_time = $dt;
        $osl->result = $msg;
        $osl->save();
    }
}
