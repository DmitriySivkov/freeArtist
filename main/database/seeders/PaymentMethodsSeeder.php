<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		foreach (PaymentMethod::PAYMENT_METHODS as $paymentMethod) {
			PaymentMethod::create([
				'name' => $paymentMethod,
			]);
		}
    }
}
