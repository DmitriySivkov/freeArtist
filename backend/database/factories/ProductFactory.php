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
    	$ingredient1 = $this->faker->text(5);
		$ingredient2 = $this->faker->text(5);
		$ingredient3 = $this->faker->text(5);
    	$composition = collect([
			$ingredient1 . 'Ingr' => $ingredient1 . 'IngrDescription',
			$ingredient2 . 'Ingr' => $ingredient2 . 'IngrDescription',
			$ingredient3 . 'Ingr' => $ingredient3 . 'IngrDescription',
		]);
		return [
			'producer_id' => Producer::query()
				->inRandomOrder()
				->first()
				->id,
			'title' => $this->faker->text(10) . 'Product',
			'composition' => $composition->toJson(),
		];
    }
}
