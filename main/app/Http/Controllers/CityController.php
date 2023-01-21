<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
	{
		return City::query()
			->when($request->has('query'), fn (Builder $builder) =>
				$builder->where('city', 'like',$request->get('query') . '%')
			)
			->get();
	}
}
