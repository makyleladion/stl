<?php

namespace App\Http\Controllers\Api;

/*
This controller serves as a status checker for the STL API server
returns a JSON response with a "status" index set to true
*/

use App\Http\Controllers\Controller;


class ServerPingController extends Controller
{   
    public function index() {
        return json_encode([ "status" => true ]);
    }
    
}
