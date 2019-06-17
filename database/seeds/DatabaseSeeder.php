<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->initialData();
    }

    private function initialData()
    {
        factory(App\User::class)->create([
            'name' => 'Joshua Paylaga',
            'email' => 'joshua@stl.ph',
            'password' => bcrypt('testpass'),
            'status' => 'active',
            'is_admin' => true,
        ]);
    }
}
