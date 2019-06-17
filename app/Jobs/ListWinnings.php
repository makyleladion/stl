<?php

namespace App\Jobs;

use App\WinningResult;
use App\WinningTicket;
use App\System\Games\GamesFactory;
use App\System\Services\PayoutService;
use App\System\Services\WinningService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use App\System\Games\RetrieveWinningBets;
use App\System\Services\CachingService;

class ListWinnings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $drawDate;
    
    protected $schedKey;
    
    /**
     * Payout Service.
     *
     * @var PayoutService
     */
    protected $payoutService;
    
    /**
     * Winning Service.
     *
     0     * @var WinningService1
     */
    protected $winningService;
    
    protected $cachingService;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($drawDate, $schedKey)
    {
        $this->payoutService = new PayoutService();
        $this->winningService = new WinningService();
        $this->cachingService = new CachingService();
        
        $this->drawDate = $drawDate;
        $this->schedKey = $schedKey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {        
        $drawDate = $this->drawDate;
        $schedKey = $this->schedKey;
        
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
        
        $cacheKey = sha1(CachingService::CACHE_WINNERS.$schedKey.$drawDate);
        Cache::put($cacheKey, 0, Carbon::tomorrow(env('APP_TIMEZONE')));
        
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
        
        // Store number of winners to cache
        $this->cachingService->setRawAmount(CachingService::CACHE_WINNERS, count($winningTicketDbInsert), $schedKey, $drawDate);
        
        DB::commit();
    }
}
