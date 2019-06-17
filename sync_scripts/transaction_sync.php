<?php

// server token and endpoint
$token = "Anklvpm33lrGp7HtLlBJ6yeS529gxahsl27RkjinZt9xwWYbCkpr6WhqRqW4"; // update token
$url = "https://test.system.stl.ph/api/new_transaction?api_token=$token"; // update URL if necessary
$header = array("Content-Type: application/json", "authorization: Bearer $token"); // Authorization header does not seem to work - del.

// local db - update this with local creds
$username = "root";
$password = "odel";
$host = "localhost";
$db = "stl_v1";
$local_transactions_tbl = "transactions";
$local_tickets_tbl = "tickets";
$local_bet_tbl = "bets";


// TODO Select unsynced transactions
$db_conn = mysqli_connect($host,$username,$password,$db);

if (mysqli_connect_errno()) {
  echo "Error connecting: " . mysqli_connect_error();
  die();
}

$txresults = mysqli_query($db_conn, "SELECT * FROM $local_transactions_tbl WHERE sync = 0");



while ($tx = mysqli_fetch_assoc($txresults)) {
	$payload = $tx;
	$payload['transaction_id'] = $tx['id'];
	$payload['tickets'] = array();
	
	$ticketresults = mysqli_query($db_conn, "SELECT * FROM $local_tickets_tbl WHERE transaction_id = ".$tx['id']);
	while ($ticket = mysqli_fetch_assoc($ticketresults)) { 
		$ticketToAdd = $ticket;
		$ticketToAdd['bets'] = array();
		$betresults = mysqli_query($db_conn, "SELECT * FROM $local_bet_tbl WHERE transaction_id = ".$ticket['id']);
		while ($bet = mysqli_fetch_assoc($betresults)) { 
			$ticketToAdd['bets'][] = $bet;
		}
		$payload['tickets'][] = $ticketToAdd;
	}
	$data = json_encode($payload);
	$response = curlAPI($url, $data, $header, "POST");
	echo "Result: <br>";
	echo "<br>";
	
	// check if success. to confirm: null response if failed
	if ($response!=null) {
		// Update local db 
		mysqli_query($db_conn, "UPDATE transactions_mock SET sync = 1 WHERE id = ".$tx['id']);
		echo "Transaction id {$tx['id']} has been synced.<br>";
	}
	
}


mysqli_close($db_conn);


function curlAPI($url, $data, $header, $request_type = "POST") {
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
        $error = new stdClass();
        $error->message = curl_error($curl);
        $error->error = curl_errno($curl);
        $error->url = $url;
        $error->data = json_decode($data);
        $error->header = $header;
        $error->request_type = $request_type;
        $error->response = json_decode($json_response);
        curl_close($curl);
        return json_decode(json_encode($error));
    }

    curl_close($curl);
    return json_decode($json_response);
}
