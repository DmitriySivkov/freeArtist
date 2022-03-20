<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use Illuminate\Http\JsonResponse;

class ProducerController extends Controller
{
	/**
	 * @return JsonResponse
	 */
	public function index()
	{
		$producers = Producer::orderBy('title', 'asc')->get();

		return response()->json($producers);
	}
}
