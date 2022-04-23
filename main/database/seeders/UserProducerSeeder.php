<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Producer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserProducerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Producer::factory(10)->create();
        User::factory(10)->create();
    }
}
