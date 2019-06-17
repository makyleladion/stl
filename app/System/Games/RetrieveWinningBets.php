<?php
namespace App\System\Games;

use App\WinningResult;
use App\System\Games\Swertres\SwertresService;
use App\System\Games\Swertres\SwertresGame;
use App\Bet;
use App\System\Games\Pares\ParesGame;
use App\System\Games\SwertresSTL\SwertresSTLGame;
use App\System\Games\SwertresSTLLocal\SwertresSTLLocalGame;

class RetrieveWinningBets
{
    public static function get($gameName, $drawDate, $schedKey)
    {
        $win = WinningResult::where([
            'game' => $gameName,
            'result_date' => $drawDate,
            'schedule_key' => $schedKey,
        ])->firstOrFail();
        
        $bets = [];
        
        if ($gameName == SwertresGame::name()) {
            $bets = self::swertresWins($win);
        } else if ($gameName == SwertresSTLGame::name()) {
            $bets = self::swertresSTLWins($win);
        } else if ($gameName == SwertresSTLLocalGame::name()) {
            $bets = self::swertresSTLLocalWins($win);
        } else {
            throw new \Exception('Invalid game name.');
        }
        
        return ['win' => $win, 'bets' => $bets];
    }
    
    private static function swertresWins(WinningResult $win)
    {
        $service = new SwertresService();
        $perms = $service->permutation($win->number);
        $finalPerms = [];
        foreach ($perms as $perm) {
            $finalPerms[] = implode('', $perm);
        }
        
        // Retrieve winning tickets        
        $bets = Bet::whereHas('ticket', function ($q) use ($win) {
            $q->where([
                'result_date' => $win->result_date,
                'schedule_key' => $win->schedule_key,
                'is_cancelled' => false,
            ]);
        })->where(function($q1) use ($win, $finalPerms) {
            $q1->whereIn('number', $finalPerms)->where('type', SwertresGame::TYPE_RAMBLED)
            ->orWhere(function($q2) use ($win) {
                $q2->where('number', $win->number)->where('type', SwertresGame::TYPE_STRAIGHT);
            });
        })->where('game', $win->game)->get();
        
        return $bets;
    }
    
    private static function paresWins(WinningResult $win)
    {
        $number = $win->number;
        $versions = [];
        
        list($left, $right) = explode(':', $number);
        
        // left
        if (intval($left) < 10) {
            $left = ltrim($left, '0');
            $versions[] = $left.':'.$right;
            
            $left = str_pad($left, 2, '0', STR_PAD_LEFT);
            $versions[] = $left.':'.$right;
        }
        $versions[] = $left.':'.$right;
        
        // right
        if (intval($right) < 10) {
            $right = ltrim($right, '0');
            $versions[] = $right.':'.$right;
            
            $left = str_pad($right, 2, '0', STR_PAD_LEFT);
            $versions[] = $left.':'.$right;
        }
        $versions[] = $left.':'.$right;
        
        $bets = Bet::whereHas('ticket', function($q) use ($win) {
            $q->where([
                'result_date' => $win->result_date,
                'schedule_key' => $win->schedule_key,
                'is_cancelled' => false,
            ]);
        })->whereIn('number', $versions)
        ->where('game', $win->game)->get();
        
        return $bets;
    }
    
    private static function swertresSTLWins(WinningResult $win)
    {
        return self::swertresWins($win);        
    }
    
    private static function swertresSTLLocalWins(WinningResult $win)
    {
        return self::swertresWins($win);
    }
}
