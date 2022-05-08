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
		$producers = Cache::remember('producerList', 600, function() {
			return Producer::orderBy('title', 'asc')
				->get();
		});

		return response()->json($producers);
	}

	public function show(Producer $producer)
	{
		return response()->json($producer->load(['products']));
	}
}
