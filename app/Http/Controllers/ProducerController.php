<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use Illuminate\Support\Facades\Cache;

class ProducerController extends Controller
{
	public function index()
	{
		$producers = Cache::remember('producers', 60, function() {
			return Producer::orderBy('title', 'asc')->get();
		});

		return response()->json([
			'producers' => $producers
		]);
	}
}
