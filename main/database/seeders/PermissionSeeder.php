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
		$roleProducer = Role::where('name', '=', 'producer')->first('id');
		foreach (Permission::PERMISSIONS_PRODUCER as $permission) {
			Permission::create([
				'name' => $permission['name'],
				'role_id' => $roleProducer->id,
				'display_name' => $permission['display_name'],
				'description' => $permission['description']
			]);
		}
    }
}
