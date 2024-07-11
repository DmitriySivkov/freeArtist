<?php

namespace App\Http\Controllers\Yookassa;

use App\Enums\TransactionEnum;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class YookassaController extends Controller
{
    public function status(Request $request)
	{
		$data = $request->input('object');

		$transaction = Transaction::where('uuid', $data['metadata']['transaction_uuid'])
			->first();

		if (!$transaction) {
			\Log::error("Could not find transaction with uuid: {$data['metadata']['transaction_uuid']}");

			return ;
		}

		$transaction->update([
			'status' => TransactionEnum::YOOKASSA_STATUSES[$data['status']]
		]);
	}
}
