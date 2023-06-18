<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
//	/**
//	 * @param Request $request
//	 * @return \Illuminate\Support\Collection
//	 */
//	public function index(Request $request)
//	{
//		$q = $request->input('query', null);
//
//		$limit = 15;
//
//		$query = Tag::when($q, fn($builder) => $builder->where('name', 'like', "%{$q}%"))
//			->orderBy('name')
//			->limit($limit);
//
//		return $query->get();
//	}
}
