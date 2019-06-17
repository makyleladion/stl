<?php

use Illuminate\Database\Seeder;

class UnitTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->testData();
    }

    private function testData()
    {
        factory(App\User::class, 150)
            ->create([
                'password' => bcrypt('testpass'),
            ])
            ->each(function ($user) {

                // Outlet
                factory(App\Outlet::class, 1)
                    ->create([
                        'user_id' => $user->id,
                    ])
                    ->each(function($outlet) use ($user) {

                        // Transaction
                        factory(App\Transaction::class, 420)
                            ->create([
                                'outlet_id' =>  $outlet->id,
                                'user_id' => $user->id,
                            ])
                            ->each(function($transaction) use ($outlet, $user) {

                                // Ticket
                                factory(App\Ticket::class, 1)
                                    ->create([
                                        'transaction_id' => $transaction->id,
                                        'outlet_id' =>  $outlet->id,
                                        'user_id' => $user->id,
                                    ])
                                    ->each(function($ticket) use ($transaction, $outlet) {

                                        $ticket->bets()->save(factory(App\Bet::class)->make([
                                            'transaction_id' => $transaction->id,
                                            'outlet_id' => $outlet->id,
                                            'ticket_id' => $ticket->id,
                                        ]));
                                    });
                            });
                    });
            });
    }
}
