<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\System\Data\Timeslot;
use App\System\Utils\TimeslotUtils;
use App\System\Data\Statistics\DailySalesUsher;
use App\Outlet;

class ReportsApiController extends Controller {

    public function getDailySales() {

        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE); 
        $datetime = $data['datetime'];
        $outlet_id = $data['outlet_id'];
        list($drawDate, $timeslot) = explode(' ', $datetime);
        $isDrawSpecific = array_key_exists("schedule_key",$data);
        $origin = request()->query('origin');

        if ($isDrawSpecific) {
            if (!TimeslotUtils::drawDateTimeslotHasPassed($drawDate, $data['schedule_key'])) {
                return response()->json(['error'=>'Not yet allowed to view timeslot draw sales.']);
            }
        }

        try {
            $schedKey = $isDrawSpecific ? $data['schedule_key'] : Timeslot::getKeyByTime($timeslot);
        } catch (\Exception $e) {
            $schedKey = null;    
        }
        $outlet = Outlet::where('id', $outlet_id)->firstOrFail();
        $dailySales = new DailySalesUsher($outlet, $drawDate, $schedKey, $origin);
        $output = $dailySales->toArray(true);
        $output['outlet'] = [
            'name' => $outlet->name,
            'address' => $outlet->address
        ];
        return response()->json($output);
        
    }

    

}