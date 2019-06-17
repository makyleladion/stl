<?php

use Illuminate\Database\Seeder;
use App\User;
use App\DefaultOutlet;

class MockInitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mainUser = User::where('email','richard@stl.ph')->first();
        if (!$mainUser) {
            $user = User::create([
                'name' => 'Richard Illescas',
                'email' => 'richard@stl.ph',
                'password' => bcrypt('testpass'),
                'is_admin' => false,
            ]);
            $this->mockData($user);
        } else {
            $this->mockData($mainUser);
        }
    }
    
    private function mockData($mainUser) {
        
        // Create outlets related to 1 owner.
        factory(App\Outlet::class, 150)
            ->create([
                'user_id' => $mainUser->id,
                'is_affiliated' => true,
            ])->each(function($outlet) {
                
                // Create tellers for reach outlets
                factory(App\User::class, 1)
                ->create([
                    'password' => bcrypt('testpass'),
                ])->each(function($user) use ($outlet) {
                    
                    // Assign each tellers to outlet
                    DefaultOutlet::create([
                        'user_id' => $user->id,
                        'outlet_id' => $outlet->id,
                    ]);
                });
            });
    }
}
