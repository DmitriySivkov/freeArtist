<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		User::all()->each(function (User $user) {
			$role = \Faker\Factory::create()->numberBetween(Role::CUSTOMER, Role::PRODUCER);
			if ($role === Role::CUSTOMER)
				$user->roles()->attach(Role::CUSTOMER);
			if ($role === Role::PRODUCER)
				$user->roles()->attach([Role::CUSTOMER, Role::PRODUCER]);
		});
    }
}
