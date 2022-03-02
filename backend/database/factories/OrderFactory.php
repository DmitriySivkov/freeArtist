<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Producer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::query()
                ->where('role_id', 1)
                ->inRandomOrder()
                ->first()
                ->id,
            'producer_id' => Producer::query()
                ->inRandomOrder()
                ->first()
                ->id,
            'products' => Product::query()
				->inRandomOrder()
				->limit(4)
				->pluck('id'),
            'payment_method' => $this->faker->numberBetween(1,2),
            'status' => 1,
        ];

    }
}
