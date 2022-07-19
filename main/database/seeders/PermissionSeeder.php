<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		/** postgres specific command */
		DB::statement("ALTER SEQUENCE permissions_id_seq RESTART WITH 1");

		$roleProducer = Role::where('name', '=', 'producer')->first();
		foreach (Permission::PERMISSIONS_PRODUCER as $permission) {
			$permission = Permission::create([
				'name' => $permission['name'],
				'display_name' => $permission['display_name'],
				'description' => $permission['description']
			]);
			$roleProducer->attachPermission($permission);
		}
    }
}
