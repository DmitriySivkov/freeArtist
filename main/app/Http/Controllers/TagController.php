<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		$withAllTags = $request->input('withAllTags');
		$categories = $request->input('categories');

		$tags = [
			'items' => Tag::query()
				->when($categories, function($query) use ($categories) {
					$query->whereHas('categories', fn($query) => $query->whereIn('id', $categories));
				})
				->get()
		];

		if ($withAllTags) {
			$tags['all_tags'] = Tag::all();
		}

		return $tags;
	}
}
