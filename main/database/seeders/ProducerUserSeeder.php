<?php

namespace Database\Seeders;

use App\Models\Producer;
use App\Models\ProducerUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class ProducerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$producers = [];
		User::whereHas('roles', function (Builder $query) {
			$query->where('roles.id', Role::PRODUCER);
		})
			->each(function (User $user) use (&$producers) {
				$randomProducerId = \Faker\Factory::create()->numberBetween(
					Producer::first()->id,
					Producer::OrderBy('id', 'desc')->first()->id,
				);

				if (!in_array($randomProducerId, $producers)) {
					$user->producers()
						->attach($randomProducerId, ['rights' => collect([ProducerUser::RIGHT_OWNER])]);
				}

				$producers[] = $randomProducerId;
			});
    }
}
