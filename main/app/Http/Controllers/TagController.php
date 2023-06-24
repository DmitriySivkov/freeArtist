<?php

namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{
	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function index()
	{
		return Tag::all();
	}
}
