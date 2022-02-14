<?php

namespace Database\Factories;

use App\Models\Producer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
    	$ingredient = $this->faker->text(5);
    	$composition = collect([
			$ingredient => $ingredient . 'Description'
		]);
		return [
			'producer_id' => Producer::query()
				->inRandomOrder()
				->first()
				->id,
			'title' => $this->faker->text(10) . 'Product',
			'composition' => $composition->toJson(),
			'payment_method_id' => $this->faker->numberBetween(1,2),
		];
    }
}
