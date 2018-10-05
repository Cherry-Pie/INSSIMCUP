<?php

use Illuminate\Database\Seeder;

use App\Models\Team;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Team::create([
            'title' => 'Arsenal',
        ]);
        Team::create([
            'title' => 'Chelsea',
        ]);
        Team::create([
            'title' => 'Manchester City',
        ]);
        Team::create([
            'title' => 'Liverpool',
        ]);
    }
}
