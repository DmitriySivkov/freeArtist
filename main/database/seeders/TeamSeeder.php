<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		/** postgres specific command */
		DB::statement("ALTER SEQUENCE teams_id_seq RESTART WITH 1");

        foreach (Team::TEAMS as $team) {
			Team::create([
				'name' => $team['name'],
				'display_name' => $team['display_name'],
				'description' => $team['description']
			]);
		}
    }
}
