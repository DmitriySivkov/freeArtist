<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$roles = [
			[
				'id' => 1,
				'name' => 'producer',
				'display_name' => 'Изготовитель',
				'description' => ''
			],
			[
				'id' => 2,
				'name' => 'advertiser',
				'display_name' => 'Рекламодатель',
				'description' => ''
			],
			[
				'id' => 3,
				'name' => 'guarantor',
				'display_name' => 'Гарант',
				'description' => ''
			]
		];

		foreach ($roles as $role) {
			Role::create([
				'name' => $role['name'],
				'display_name' => $role['display_name'],
				'description' => $role['description']
			]);
		}
    }
}
