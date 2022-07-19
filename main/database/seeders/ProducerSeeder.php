<?php

namespace Database\Seeders;

use App\Models\Producer;
use App\Models\ProducerUser;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProducerSeeder extends Seeder
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

		$producerRole = Role::where('name', '=', 'producer')
			->first();

		User::inRandomOrder()
			->limit(3)
			->get()
			->each(function (User $user) use ($producerRole) {
				$team = Team::create([
					'name' => $user->id . '_user_producer',
					'display_name' => \Faker\Factory::create()->unique()->firstName . ' Company',
					'description' => ''
				]);

				$user->attachRole($producerRole, $team);

				$producer = Producer::create([
					'team_id' => $team->id
				]);

				ProducerUser::create([
					'producer_id' => $producer->id,
					'user_id' => $user->id,
					'user_active' => ProducerUser::PRODUCER_USER_ACTIVE
				]);
		});

    }
}
