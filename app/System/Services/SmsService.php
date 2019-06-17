<?php
namespace App\System\Services;

use Nexmo\Laravel\Facade\Nexmo;
use App\WinningResult;
use App\System\Games\GamesFactory;
use App\System\Data\Timeslot;
use Carbon\Carbon;
use App\System\Data\Outlet;
use App\MobileNumber;

class SmsService
{
    private $mobile;
    
    public function __construct($mobile)
    {
        if (is_array($mobile)) {
            $this->mobile = [];
            foreach ($mobile as $m) {
                $this->mobile[] = $this->filter($m);
            }
        } else if (is_string($mobile)) {
            $this->mobile[] = $this->filter($mobile);
        } else if ($mobile instanceof MobileNumber) {
            $this->mobile = [];
            foreach ($mobile as $m) {
                $this->mobile[] = $this->filter($m->mobile_number);
            }
        }
    }
    
    public function sendAdminSmsRegistration()
    {
        return $this->text('Your mobile number has been added to the list of numbers to be sent with STL admin notifications.');
    }
    
    public function sendAdminDeleteMessage()
    {
        return $this->text('Your mobile number has been deleted from the list of numbers in STL admin. If this is a mistake, please contact tech support.');
    }
    
    public function sendAdminResultsAndCurrentSales($drawDate, $schedKey)
    {
        try {
            $transactionService = new TransactionService();
            $winningService = new WinningService();
            
            $dailySales = $transactionService->getTotalAmountByDateAndTime($drawDate, $schedKey);
            $numberOfTickets = $transactionService->getTotalTickets($drawDate, $schedKey);
            $numberOfWinnings = $winningService->countWinnings($drawDate, $schedKey);
            
            $results = WinningResult::where([
                'result_date' => $drawDate,
                'schedule_key' => $schedKey,
            ])->get();
            
            $timestamp = sprintf("%s %s", $drawDate, Timeslot::getTimeByKey($schedKey));
            $timeCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, env('APP_TIMEZONE'));
            $drawDateCarbon = Carbon::createFromFormat('Y-m-d', $drawDate, env('APP_TIMEZONE'));
            
            $message = sprintf("Results and stats for the draw on %s\n\n", $timeCarbon->toCookieString());
            
            foreach ($results as $result) {
                $message .= sprintf("%s: %s\n", GamesFactory::getGameLabelByGameName($result->game), $result->number);
            }
            
            $message .= "\n";
            
            $message .= sprintf("Sales: PHP %s\n", number_format($dailySales, 0, '.', ','));
            $message .= sprintf("Tickets: %d\n", $numberOfTickets);
            $message .= sprintf("Winners: %d\n", $numberOfWinnings);
            
            $message .= "\n";
            
            $message .= sprintf("Current Total Sales: PHP %s\n", number_format($transactionService->getTotalAmountByDate($drawDateCarbon), 0, '.', ','));
            $message .= sprintf("Current Winnings Allocation: PHP %s\n", number_format($winningService->totalWinningAmountAllocation($drawDate), 0, '.', ','));
            
            return $this->text($message);
        } catch (\Exception $e) {
            report($e);
        }
    }
    
    public function sendCustomerSmsRegistration(Outlet $outlet)
    {
        $message = "Your mobile number has been added to the list of numbers to be sent with STL results notification.\n\n";
        $message .= sprintf("Outlet Address: %s\n", $outlet->address());
        
        return $this->text($message);
    }
    
    public function sendCustomerResults($drawDate, $schedKey)
    {
        try {
            $results = WinningResult::where([
                'result_date' => $drawDate,
                'schedule_key' => $schedKey,
            ])->get();
            
            $timestamp = sprintf("%s %s", $drawDate, Timeslot::getTimeByKey($schedKey));
            $timeCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, env('APP_TIMEZONE'));
            $drawDateCarbon = Carbon::createFromFormat('Y-m-d', $drawDate, env('APP_TIMEZONE'));
            
            $message = sprintf("Results for the draw on %s\n\n", $timeCarbon->toCookieString());
            foreach ($results as $result) {
                $message .= sprintf("%s: %s\n", GamesFactory::getGameLabelByGameName($result->game), $result->number);
            }
            $message .= "\n";
            $message .= "Thank you!";
            
            return $this->text($message);
            
        } catch (\Exception $e) {
            report($e);
        }
    }
    
    public function text($message)
    {
        $message = "3a8 Gaming - STL\n\n" . $message;
        $responses = [];
        foreach ($this->mobile as $m) {
            $responses[] = Nexmo::message()->send([
                'to'   => $m,
                'from' => '3a8 Gaming',
                'text' => $message,
            ]);
        }
        
        return $responses;
    }
    
    public function filter($mobile)
    {
        $first = mb_substr($mobile, 0, 2);
        if ($first != '63') {
            $mobile = ltrim($mobile, '0');
            $mobile = '63' . $mobile;
        }
        
        return $mobile;
    }
}
