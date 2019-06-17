<?php

use Faker\Generator as Faker;
use App\System\Games\Swertres\SwertresGame;
use App\System\Games\Pares\ParesGame;

$factory->define(App\Bet::class, function (Faker $faker) {
    $outlet_ids = App\Outlet::pluck('id');
    $transaction_ids = App\Transaction::pluck('id');
    $ticket_ids = App\Ticket::pluck('id');
    
    $betArray = [
        'transaction_id' => $transaction_ids->random(),
        'outlet_id' => $outlet_ids->random(),
        'ticket_id' => $ticket_ids->random(),
        'amount' => rand(1,20),
    ];
    
    if (rand(0,1) == 1) {
        $types = [
            'straight',
            'rambled',
        ];
        
        $betType = $types[rand(0,1)];
        $d1 = (string) rand(0, 9);
        $d2 = (string) rand(0, 9);
        $d3 = (string) rand(0, 9);
        $number = $d1.$d2.$d3;
       
        $betArray['type'] = $betType;
        $betArray['number'] = $number;
        $betArray['game'] = SwertresGame::name();
    } else {
        
        $d1 = (string) rand(1, 40);
        $d2 = (string) rand(1, 40);
        $number = $d1 . ':' . $d2;
        
        $betArray['type'] = 'none';
        $betArray['number'] = $number;
        $betArray['game'] = ParesGame::name();
    }
    
    return $betArray;
});
