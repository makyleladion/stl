<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Outlet;
use App\Http\Controllers\Controller;
use App\System\Data\Timeslot;
use App\System\Games\GamesFactory;
use App\System\Services\WinningService;
use App\System\Services\PayoutService;
use App\System\Utils\TimeslotUtils;

class OfflineSyncController extends Controller
{
    private $outlet;
    
    public function __construct()
    {
        
    }
    
    public function getUpdatedWinners()
    {
        $service = new WinningService();
        $drawDate = \request()->query('draw_date', date('Y-m-d'));
        if (!TimeslotUtils::validateDate($drawDate)) {
            throw new \Exception('Provided value for draw_date has invalid input: ' . $drawDate);
        }
        $winnings = [];
        foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
            $winnings[$key] = $service->countWinnings($drawDate, $key, $this->outlet);
        }
        
        return response()->json($winnings);
    }
    
    public function getUpdatedPayouts()
    {
        $service = new PayoutService($this->outlet);
        $drawDate = \request()->query('draw_date', date('Y-m-d'));
        if (!TimeslotUtils::validateDate($drawDate)) {
            throw new \Exception('Provided value for draw_date has invalid input: ' . $drawDate);
        }
        $payouts = $service->getPayoutsPerDate($drawDate, 0, 100);
        $serializePayouts = [];
        foreach ($payouts as $payout) {
            $serializePayouts[] = serialize($payout);
        }
        
        return response()->json($serializePayouts);
    }


    public function getWinners() {
        $drawDate = \request()->query('draw_date', null);
        $winnings = [];
        $drawDate = ($drawDate && strlen($drawDate) > 0) ? $drawDate : date('Y-m-d');
        $winningService = new WinningService();
        $winners = $winningService->getWinningBetsStored($drawDate);
        
        $winnersArr = [];

        foreach ($winners as $winnerModel) {
            $winnersArr[] = [
                'ticket_number' => $winnerModel->ticketNumber(),
                'outlet_name' => $winnerModel->outletName(),
                'outlet_id' => $winnerModel->outletId(),
                'teller' => $winnerModel->teller(),
                'customer' => $winnerModel->customer(),
                'bet' => $winnerModel->bet(),
                'game' => $winnerModel->game(),
                'type' => $winnerModel->type(),
                'amount' => $winnerModel->amount(),
                'winner_prize' => $winnerModel->winningPrize(),
                'draw_date_time' => $winnerModel->drawDateTime(),
            ];
        }

        return response()->json($winnersArr);
    }

    public function getWinnings()
    {
        $isAdmin = auth()->user()->is_admin;
        $drawDate = \request()->query('draw_date', null);
        $winnings = [];
        $drawDate = ($drawDate && strlen($drawDate) > 0) ? $drawDate : date('Y-m-d');
        $payoutService = new PayoutService();
        foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
            foreach (GamesFactory::getGameNames() as $gameName) {
                $gameLabel = GamesFactory::getGameLabelByGameName($gameName);
                $winning = $payoutService->getWinningResult($gameName, $drawDate, $key);
                
                if ($isAdmin) {
                    $winnings[$key][$gameName] = $winning;
                    
                } else {
                    $winnings[$key][$gameName] = (TimeslotUtils::isDrawTimePassed($drawDate, $key)) ? $winning : '';
                }
            }
        }
        $winnings['draw_date'] = $drawDate;
        return response()->json($winnings);
    }
}
