<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use Carbon\Carbon;

use App\Bet;
use App\OfflineSyncLog;
use App\Ticket;
use App\Transaction;
use App\WinningResult;
use App\User;

use App\Jobs\ListWinnings;

use App\System\Data\Timeslot;
use App\System\Games\Pares\ParesGame;
use App\System\Games\Pares\ParesService;
use App\System\Games\Swertres\SwertresGame;
use App\System\Games\SwertresSTL\SwertresSTLGame;


use App\Console\Commands\Utils\ApiUtils;

class WinnersResultsAndPayoutsOfflineSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stl:winnings-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes winning results, winners and payouts to main server. Usage: stl:winnings-sync <your_token> <STL link>/api. Example: php artisan stl:transaction-sync 123..60 https://testing.systme.stl.phss/api';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $token = env('OFFLINE_API_TOKEN', "");
        $url = env('OFFLINE_API_URL', "");

        $winners_and_payouts_url = $url."/winning-results";
        $ping_server_url = $url."/ping_server";

        if (!is_numeric(strpos($ping_server_url, 'api_token'))) {
            $ping_server_url .= "?api_token=$token";
        }

        if (!is_numeric(strpos($winners_and_payouts_url, 'api_token'))) {
            $winners_and_payouts_url .= "?api_token=$token";
        }
        

        $header = array("Content-Type: application/json", "authorization: Bearer $token");
        $response = ApiUtils::curlAPI($ping_server_url, "", $header, "GET");

        
        
        if (!is_object($response)) {
            $msg_log = "Server not online or wrong URL/token. Sync postponed.";
            ApiUtils::generateOfflineSyncLog($msg_log);
            return 0;
        }
        
        $response = ApiUtils::curlAPI($winners_and_payouts_url, "", $header, "GET");

        if (is_object( $response ) ) {
            $response = (array) $response;
            
            $draw_date = $response['draw_date'];
            
            $user = User::where('api_token','=',$token)->get()->toArray();
            if (!$user) {
                ApiUtils::generateOfflineSyncLog("Error in syncing results: User API token is not found in local db.");
                exit();
            }
            $user = $user[0];
            
            foreach ($response as $key => $value) {
                if ($key=='draw_date') {
                    continue;
                }

                if ($key!=Timeslot::TIMESLOT_MORNING && $key!=Timeslot::TIMESLOT_AFTERNOON && $key!=Timeslot::TIMESLOT_EVENING) {
                    continue;
                }

                if ($key==Timeslot::TIMESLOT_MORNING) {
                    $timeSlotLog = "Morning";
                } else if ($key==Timeslot::TIMESLOT_AFTERNOON) {
                    $timeSlotLog = "Afternoon";
                } else {
                    $timeSlotLog = "Evening";
                }

                 

                $schedKey = $key;
                
                /* NOTE: Removed admin checker here since offline app should only utilize teller-linked API token
                 * Any difference of the offline results and main cloud server will automatically invalidate winning results
                 * in the offline. - Leodel
                 */
                
                try {
                    $swertresGameName = SwertresGame::name();
                    $swertres       = $value->$swertresGameName;
                    $swertresSTLGameName = SwertresSTLGame::name();
                    $swertresSTL    = $value->$swertresSTLGameName;

                    $paresGameName = ParesGame::name();
                    $pares          = $value->$paresGameName;
                    $paresExploded  = explode(":",$pares);
                    $pares1         = count($paresExploded) == 2 ? $paresExploded[0] : "";
                    $pares2         = count($paresExploded) == 2 ? $paresExploded[1] : "";
                    
                    if (!$swertres && !$swertresSTL && !$pares1 && !$pares2) {
                        throw new \Exception("Please input either Swertres Nat't, Swertres STL, or Pares results. Do not leave all results blank.");
                    }
        
                    DB::beginTransaction();

                    $this->swertresCommonWinning($user, $schedKey, $swertres, SwertresGame::name(), $draw_date);
                    $this->swertresCommonWinning($user, $schedKey, $swertresSTL, SwertresSTLGame::name(), $draw_date);
                    $this->paresWinning($user, $schedKey, $pares1, $pares2, $draw_date);
                    
                    DB::commit();
                    
                    ApiUtils::generateOfflineSyncLog("Successfully synced: ". $draw_date . " " . $timeSlotLog . " results");
                    echo "Results sync is successful.";
                } catch (QueryException $e) {
                    DB::rollBack();
                    ApiUtils::generateOfflineSyncLog($e);
                } catch (FatalThrowableError $e) {
                    DB::rollBack();
                    ApiUtils::generateOfflineSyncLog($e);
                } catch (\Exception $e) {
                    DB::rollBack();
                    ApiUtils::generateOfflineSyncLog($e);
                }   
            }
        }
        
    }

    // IMPORTANT TODO: Place ALL results-related functions to a utils folder for DashboardController and Commands to reuse them.
    // Below are duplicate declarations just for urgent reasons. - Leodel

    private function swertresCommonWinning($user, $schedKey, $result1, $game, $draw_date = null)
    {

        if (!$draw_date) {
            throw new \Exception("swertresCommonWinning: Draw date is not set.");
        }

        if (!$result1 || strlen($result1) <= 0) {
            return;
        }
        
        $existing = WinningResult::where('result_date', '=', $draw_date)
            ->where('schedule_key', '=', $schedKey)
            ->where('game', '=', $game)
            ->first();

        if ($existing) {
            $existing->number = $result1;
            $existing->save();
        } else {
            WinningResult::create([
                'user_id' => $user['id'],
                'game' => $game,
                'number' => $result1,
                'result_date' => $draw_date,
                'schedule_key' => $schedKey,
            ]);
        }
    }

    private function paresWinning($user, $schedKey, $result1, $result2, $draw_date = null)
    {

        if (!$draw_date) {
            throw new \Exception("paresWinning: Draw date is not set.");
        }

        if ((((!$result1 || strlen($result1)) <= 0) && $result2) || ($result1 && (!$result2 || strlen($result2) <= 0))) {
            throw new \Exception('If you supply results for Pares, please input both numbers.');
        }
        
        if (!$result1 && !$result2) {
            return;
        }
        
        $service = new ParesService();
        $result = $service->addLeadingZero(sprintf("%s:%s", $result1, $result2));
        $existing = WinningResult::where('result_date', '=', $draw_date)
            ->where('schedule_key', '=', $schedKey)
            ->where('game', '=', ParesGame::name())
            ->first();
        

        if ($existing) {
            $existing->number = $result;
            $existing->save();
        } else {
            WinningResult::create([
                'user_id' => $user['id'],
                'game' => ParesGame::name(),
                'number' => $result,
                'result_date' => $draw_date,
                'schedule_key' => $schedKey,
            ]);
        }
    }
}
