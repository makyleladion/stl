<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\System\Services\PayoutService;
use App\System\Games\GamesFactory;
use App\System\Games\RetrieveWinningBets;
use App\System\Services\WinningService;
use App\WinningResult;
use App\Bet;
use App\WinningTicket;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class listWinningBets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stl:winnings {drawDate} {schedKey}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Traverse the database to accumulate winners from draw.';
    
    /**
     * Payout Service.
     * 
     * @var PayoutService
     */
    protected $payoutService;
    
    /**
     * Winning Service.
     * 
     * @var WinningService1
     */
    protected $winningService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->payoutService = new PayoutService();
        $this->winningService = new WinningService();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        $drawDate = $this->argument('drawDate');
        $schedKey = $this->argument('schedKey');
        
        $winningQuery = WinningResult::where([
            'result_date' => $drawDate,
            'schedule_key' => $schedKey,
        ])->get();
        
        // Retrieve currently stored winning tickets
        $currWinTixIds = [];
        foreach ($winningQuery as $win) {
            $winningTickets = $win->winningTickets()->get();
            foreach ($winningTickets as $winTix) {
                $currWinTixIds[] = $winTix->id;
            }
        }
        
        DB::beginTransaction();
        
        // Remove current winning tickets
        if (count($currWinTixIds) > 0) {
            DB::table('winning_tickets')->whereIn('id', $currWinTixIds)->delete();
        }
        
        // Retrieve bets on new or edited winning results
        $winningTicketDbInsert = [];
        foreach (GamesFactory::getGameNames() as $game) {
            try {
                $winAndBets = RetrieveWinningBets::get($game, $drawDate, $schedKey);
                foreach ($winAndBets['bets'] as $bet) {
                    $winningTicketDbInsert[] = [
                        'winning_result_id' => $winAndBets['win']->id,
                        'ticket_id' => $bet->ticket()->firstOrFail()->id,
                        'bet_id' => $bet->id,
                        'created_at' => Carbon::now(env('APP_TIMEZONE')),
                        'updated_at' => Carbon::now(env('APP_TIMEZONE')),
                    ];
                }
            } catch (ModelNotFoundException $e) {
                // ignore
            }
        }
        
        // Insert new winning tickets
        WinningTicket::insert($winningTicketDbInsert);
        
        DB::commit();
    }
}
