<?php

namespace App\Http\Controllers\Personal;

use App\Models\Tag;
use App\Http\Controllers\Controller;

class PersonalTagController extends Controller
{
	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function index()
	{
		return Tag::all();
	}
}
