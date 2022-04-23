<?php

namespace Database\Factories;

use App\Models\Producer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
    	$roleId = $this->faker->numberBetween(Role::CUSTOMER, ROLE::PRODUCER);
    	$producerId = $roleId == Role::CUSTOMER ?
			null :
			Producer::query()
				->inRandomOrder()
				->first()
				->id;
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->email,
            'email_verified_at' => now(),
            'password' => 'test123',
            'role_id' => $roleId,
			'producer_id' => $producerId,
            'remember_token' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
