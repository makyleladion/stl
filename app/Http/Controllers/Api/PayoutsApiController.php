<?php
    
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\System\Services\PayoutService;

use App\Bet;
use App\Payout;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PayoutsApiController extends Controller {

    public function checkTicketForPayout() {
        try {
            $inputJSON = file_get_contents('php://input');
            $data = json_decode($inputJSON, TRUE); 
            $ticketNumber = $data['ticket_number'];

            $outlet_id = $data['outlet_id'];
            $service = new PayoutService();
            $winningBets = $service->getTicketWinningBets($ticketNumber, $outlet_id);

            $winningBetsOutput = [];
            $betIds = [];
            foreach ($winningBets as $bet) {
                $betIds[] = [
                    'bet' => $bet['obj']->getBet()->id,
                    'win' => $bet['win']->id,
                ];
                $output = [
                    'number' => str_replace(':','-', $bet['obj']->betNumber()),
                    'amount' => number_format($bet['obj']->amount(), 2, '.', ','),
                    'price' => number_format($bet['obj']->price(), 2, '.', ','),
                    'bet_type' => ucfirst($bet['obj']->betType()),
                ];
                $winningBetsOutput[] = $output;
            }

            return response()->json([
                'winning_bets' => $winningBetsOutput,
                'passbets' => (count($winningBetsOutput) > 0) ? Crypt::encrypt(json_encode($betIds)) : '',
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'winning_bets' => [],
                'win_error' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'winning_bets' => [],
                'win_error' => $e->getMessage(),
            ]);
        }
    }


    public function postPayout() {
        try {
            $inputJSON = file_get_contents('php://input');
            $data = json_decode($inputJSON, TRUE); 

            $passbets = $data['passbets'];
            $outlet_id = $data['outlet_id'];
            if (strlen($passbets) <= 0) {
                throw new \Exception('Please make sure that there is a winning bet in the ticket.');
            }

            $betIdsJson = Crypt::decrypt($passbets);
            $betIds = json_decode($betIdsJson);
            $betIdsWhIn = [];
            foreach ($betIds as $bid) {
                $betIdsWhIn[] = $bid->bet;
            }
            $bets = Bet::whereIn('id', $betIdsWhIn)->get();
            $toInsert = [];
            $outlet = $bets[0]->outlet()->first();

            $totalWin = 0;
            foreach ($bets as $bet) {

                $betData = new \App\System\Data\Bet($outlet, $bet);
                $totalWin += $betData->price();

                $ticketData = $betData->getTicket();
                $service = new PayoutService();
                $winningBets = $service->getTicketWinningBets($ticketData->ticketNumber(), $outlet->id);

                if ($outlet_id && $totalWin > 10000) {
                    throw new \Exception('Outlets cannot payout total prices more than PHP 10,000. Please refer the winning bettor to the main office.');
                }

                $toInsert[] = [
                    'user_id' => auth()->user()->id,
                    'outlet_id' => $outlet->id,
                    'transaction_id' => $bet->transaction_id,
                    'ticket_id' => $bet->ticket_id,
                    'bet_id' => $bet->id,
                    'winning_result_id' => $this->findWinIdByBetIdArrayFromJson($bet->id, $betIds),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            
            Payout::insert($toInsert);

            return response()->json([
                'success' => true,
                'message' => 'Payout successful.'
            ]); 
            
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function findWinIdByBetIdArrayFromJson($needle, array $fromJson)
    {
        foreach ($fromJson as $bet) {
            if ($needle == $bet->bet) {
                return $bet->win;
            }
        }
        throw new \Exception('Bet ID not found when looking for winning ID.');
    }

}