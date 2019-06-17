<?php

use Faker\Generator as Faker;

$factory->define(App\Outlet::class, function (Faker $faker) {
    $user_ids = App\User::pluck('id');

    return [
        'user_id' => $user_ids->random(),
        'name' => $faker->company,
        'address' => $faker->address,
        'status' => 'active',
        'is_affiliated' => (rand(0,1) == 1) ? true : false,
    ];
});
