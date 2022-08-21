<?php

namespace App\Http\Controllers;


use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;

class PermissionController extends Controller
{
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index()
    {
    	$permissions = Cache::remember('permissions', 60, function() {
    		return [
				'producer' => Permission::where('role_id', Role::ROLE_PRODUCER['id'])
					->get(),
				'advertiser' => [],
				'guarantor' => []
			];
    	});
    	return response()->json($permissions);
    }
}
