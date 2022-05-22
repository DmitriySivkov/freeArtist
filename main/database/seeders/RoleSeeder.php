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
    	/** postgres specific command */
		DB::statement("ALTER SEQUENCE roles_id_seq RESTART WITH 1");

		foreach (Role::ROLES as $role) {
			Role::query()->create([
				'title' => $role
			]);
		}
    }
}
