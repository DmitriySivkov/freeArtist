<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ProducerController extends Controller
{
	/**
	 * @return JsonResponse
	 */
	public function index()
	{
		$producers = Cache::remember('producers', 60, function() {
			return Producer::orderBy('title', 'asc')->get();
		});

		return response()->json($producers);
	}
}
