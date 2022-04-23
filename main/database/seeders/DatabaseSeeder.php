<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Producer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	$this->call(RoleSeeder::class);

		Producer::factory(10)->create();
		User::factory(10)->create();
		Product::factory(70)->create();
		Order::factory(10)->create();
    }
}
