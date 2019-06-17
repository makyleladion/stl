<?php

use Illuminate\Database\Seeder;
use App\DefaultOutlet;

class MockBigDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tellers = DefaultOutlet::with('user','outlet')->get();
        foreach ($tellers as $teller) {
            $user = $teller->user;
            $outlet = $teller->outlet;
            $this->testData($user, $outlet);
        }
    }
    
    private function testData($user, $outlet)
    {
        // Transaction
        factory(App\Transaction::class, 1500)
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
    }
}
