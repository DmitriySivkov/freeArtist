<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
{
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index()
    {
    	$roles = Cache::remember('roles', 60, function() {
    		return Role::all();
    	});
    	return response()->json($roles);
    }
}
