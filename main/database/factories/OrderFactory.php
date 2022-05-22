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
    	$products = Product::query()
			->inRandomOrder()
			->limit(4)
			->pluck('id')
			->map(function($productId) {
				return [
					'product_id' => $productId,
					'amount' => $this->faker->numberBetween(1,10)
				];
			});

        return [
            'user_id' => User::query()
                ->inRandomOrder()
                ->first()
                ->id,
            'producer_id' => Producer::query()
                ->inRandomOrder()
                ->first()
                ->id,
            'products' => $products,
            'payment_method' => $this->faker->numberBetween(
            	Order::ORDER_PAYMENT_METHOD_CARD,
				Order::ORDER_PAYMENT_METHOD_CASH
			),
            'status' => 1,
        ];

    }
}
