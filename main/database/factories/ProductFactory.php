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
    	$composition = [
			['name' => $ingredient1 . 'Ingr', 'description' => $ingredient1 . 'IngrDescription'],
			['name' => $ingredient2 . 'Ingr', 'description' => $ingredient2 . 'IngrDescription'],
			['name' => $ingredient3 . 'Ingr', 'description' => $ingredient3 . 'IngrDescription']
		];
		return [
			'producer_id' => Producer::query()
				->inRandomOrder()
				->first()
				->id,
			'title' => $this->faker->text(10) . 'Product',
			'composition' => $composition,
			'price' => $this->faker->numerify('##.##'),
			'amount' => $this->faker->numberBetween(2, 50)
		];
    }
}
