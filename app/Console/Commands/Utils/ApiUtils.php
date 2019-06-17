<?php

namespace App\Console\Commands\Utils;

use Carbon\Carbon;
use App\OfflineSyncLog;

class ApiUtils
{
    
    public static function curlAPI($url, $data, $header, $request_type = "POST") {
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

    public static function generateOfflineSyncLog($msg = "") {
        $osl = new OfflineSyncLog();
        $osl->transaction_id = 0;
        $date = Carbon::now();
        $dt = Carbon::createFromFormat('Y-m-d H:i:s', $date, env('APP_TIMEZONE'));
        $osl->sync_time = $dt;
        $osl->result = $msg;
        $osl->save();
    }

}
