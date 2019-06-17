<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use App\System\Services\OutletService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Outlet;


class OutletsApiController extends Controller {
    
    public function setOutletStatus() {

        try {
            $inputJSON = file_get_contents('php://input');
            $data = json_decode($inputJSON, TRUE); 
            $outlet_id = $data['outlet_id'];
            $to_enable = $data['enable'];
            $to_enable = (is_string($to_enable)) ? filter_var($to_enable, FILTER_VALIDATE_BOOLEAN) : $to_enable;
            $service = new OutletService();

            if ($to_enable) {
                $service->enableOutlet($outlet_id);
            } else {
                $service->disableOutlet($outlet_id);
            }

            return response()
                ->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }    
    
    public function getOutletStatus() {

        try {
            $inputJSON = file_get_contents('php://input');
            $data = json_decode($inputJSON, TRUE); 
            $outlet_id = $data['outlet_id'];
            $service = new OutletService();
            $status = $service->isDisabled($outlet_id) ? "active" : "disabled";
            return response()
                ->json(['status' => $status]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    } 
}