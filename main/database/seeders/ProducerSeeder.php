<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Producer;
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
		$permissionOwner = Permission::where('name', '=', 'owner')
			->first();

		User::inRandomOrder()
			->limit(3)
			->get()
			->each(function (User $user) use ($producerRole, $permissionOwner) {
				$producer = Producer::create();

				$team = Team::create([
					'name' => 'producer_' . $user->id . '_owner',
					'display_name' => \Faker\Factory::create()->unique()->firstName . ' Company',
					'description' => '',
					'user_id' => $user->id,
					'detailed_id' => $producer->id,
					'detailed_type' => Producer::class
				]);

				$user->attachRole($producerRole, $team);
				$user->attachPermission($permissionOwner, $team);


		});

    }
}
