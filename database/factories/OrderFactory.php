<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Producer;
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
        $products = collect([
            ['product_id' => 1, 'title' => 'шоколадный кекс'],
            ['product_id' => 2, 'title' => 'солёный крекер'],
            ['product_id' => 3, 'title' => 'пирожок с изюмом'],
            ['product_id' => 4, 'title' => 'слойка с джемом']
        ]);
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
            'products' => $products->toJson(),
            'payment_method_id' => $this->faker->numberBetween(1,2),
            'status_id' => 1,
        ];

    }
}
