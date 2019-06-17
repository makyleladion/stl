<?php

use Faker\Generator as Faker;
use App\System\Utils\GeneratorUtils;

$factory->define(App\Ticket::class, function (Faker $faker) {
    
    $user_ids = App\User::pluck('id');
    $outlet_ids = App\Outlet::pluck('id');
    $transaction_ids = App\Transaction::pluck('id');

    $timeslots = [
        'bet_schedule_morning',
        'bet_schedule_afternoon',
        'bet_schedule_evening',
    ];

    return [
        'transaction_id' => $transaction_ids->random(),
        'outlet_id' => $outlet_ids->random(),
        'user_id' => $user_ids->random(),
        'ticket_number' => GeneratorUtils::ticketNumber(),
        'result_date' => date('Y-m-d'),
        'schedule_key' => $timeslots[rand(0,2)],
        'is_cancelled' => false,
    ];
});
