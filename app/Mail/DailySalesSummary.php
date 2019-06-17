<?php

namespace App\Mail;

use App\System\Utils\TimeslotUtils;
use App\System\Utils\UserUtils;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\System\Data\Timeslot;
use App\System\Games\GamesFactory;
use App\System\Services\WinningService;
use App\System\Services\CachingService;
use App\System\Services\OutletService;
use App\System\Data\User;
use App\System\Services\PayoutService;

class DailySalesSummary extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (!auth()->user()->is_admin) {
            abort(404, 'Only privileged users are allowed.');
        }
        
        $draw_date = date('Y-m-d');
        $carbonObj = Carbon::createFromFormat('Y-m-d', $draw_date, env('APP_TIMEZONE'));
        
        $isToday = false;
        if (date('Y-m-d') == $draw_date) {
            $isToday = true;
        }
        
        $timeslots = TimeslotUtils::getIncrementalTimeslotsUntil(0, $draw_date);
        
        $winningService = new WinningService();
        $cachingService = new CachingService(null);
        $outletService = new OutletService();
        $dailySales = [];
        $numberOfTickets = [];
        $numberOfWinnings = [];
        $totalDailySales = [];
        $dailyAggregatedSales = [];
        $dailyAggregatedSalesTotals = [];
        $rawSalesData = $cachingService->getRawAmountDataByDate($draw_date);
        $rawSalesDataGameAggregated = $cachingService->getRawAmountDataByDateAndGame($draw_date);
        $winners = $winningService->getWinningBetsStored($draw_date);
        $winnersAggregated = $winningService->winnersAggregrateByDrawDateTime($winners);
        $winningAllocation = $winningService->totalWinningAmountAllocation($draw_date);
        foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
            $dailySales[$key] = (isset($rawSalesData[$key]['amount'])) ? $rawSalesData[$key]['amount'] : 0;
            $numberOfTickets[$key] = (isset($rawSalesData[$key]['tickets_count'])) ? $rawSalesData[$key]['tickets_count'] : 0;
            $numberOfWinnings[$key] = (isset($rawSalesData[$key]['winners_count'])) ? $rawSalesData[$key]['winners_count'] : 0;
            $dailyAggregatedSales[$key]['games'] = [];
            foreach (GamesFactory::getGameNames() as $game) {
                if (isset($rawSalesDataGameAggregated[$key])) {
                    $dailyAggregatedSales[$key]['games'][$game] = array_key_exists($game,$rawSalesDataGameAggregated[$key]) ? $rawSalesDataGameAggregated[$key][$game]['amount'] : 0;
                } else {
                    $dailyAggregatedSales[$key]['games'][$game] = 0;
                }
            }
            $dailyAggregatedSales[$key]['drawtime'] = date('g A', strtotime($timeslot));
        }
        $dailyAggregatedSalesTotals = $this->getAggregatedTotals($dailyAggregatedSales);
        $aggregatedTableGameHeaders = $this->getAllGameLabels();
        
        $userData = new User(auth()->user());
        $allowedIds = [];
        if (!$userData->isSuperAdmin()) {
            $allowedIds = UserUtils::leavesIds($userData);
        }
        
        $date = Carbon::now(env('APP_TIMEZONE'));
        $subject = sprintf("Daily Sales Summary as of %s", $date->toDayDateTimeString());
        return $this->subject($subject)->view('mail.dailysalessummary', [
            'title' => 'Daily Sales Summary for Today',
            'winnings' => $this->getWinnings(true, $draw_date),
            'total_amount' => $cachingService->getTotalAmountByDate($carbonObj),
            'daily_sales' => $dailySales,
            'daily_aggregated_sales' => $dailyAggregatedSales,
            'daily_aggregated_sales_totals' => $dailyAggregatedSalesTotals,
            'daily_aggregated_sales_headers' => $aggregatedTableGameHeaders,
            'number_of_tickets' => $numberOfTickets,
            'number_of_winnings' => $numberOfWinnings,
            'winners_aggregated' => $winnersAggregated,
            'winning_allocation' => $winningAllocation,
            'games' => GamesFactory::getGames(),
        ]);
    }
    
    private function getAggregatedTotals($data) {
        $totals = [];
        foreach (GamesFactory::getGameNames() as $game) {
            $totals[$game] = 0;
            foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
                $totals[$game] += $data[$key]['games'][$game];
            }
        }
        return $totals;
    }
    
    private function getAllGameLabels() {
        $labels = [];
        foreach (GamesFactory::getGameNames() as $gameName) {
            $labels[$gameName] = GamesFactory::getGameLabelByGameName($gameName);
        }
        return $labels;
    }
    
    private function getWinnings($isAdmin = false, $draw_date = null)
    {
        $winnings = [];
        $drawDate = ($draw_date && strlen($draw_date) > 0) ? $draw_date : date('Y-m-d');
        $payoutService = new PayoutService();
        foreach (Timeslot::drawTimeslots() as $key => $timeslot) {
            foreach (GamesFactory::getGameNames() as $gameName) {
                $gameLabel = GamesFactory::getGameLabelByGameName($gameName);
                $winning = $payoutService->getWinningResult($gameName, $drawDate, $key);
                if ($isAdmin) {
                    $winnings[$key][$gameLabel] = $winning;
                } else {
                    $winnings[$key][$gameLabel] = (TimeslotUtils::isDrawTimePassed($drawDate, $key)) ? $winning : '';
                }
            }
        }
        
        return $winnings;
    }
}
