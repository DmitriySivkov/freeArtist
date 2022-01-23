<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Producer;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserProducerOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create();
        Producer::factory(10)->create();
        Order::factory(10)->create();
    }
}
