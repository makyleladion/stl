<?php

use Faker\Generator as Faker;
use App\System\Utils\GeneratorUtils;

$factory->define(App\Transaction::class, function (Faker $faker) {
    $user_ids = App\User::pluck('id');
    $outlet_ids = App\Outlet::pluck('id');

    return [
        'outlet_id' => $outlet_ids->random(),
        'user_id' => $user_ids->random(),
        'transaction_code' => GeneratorUtils::transactionCode(),
        'customer_name' => $faker->name,
    ];
});
