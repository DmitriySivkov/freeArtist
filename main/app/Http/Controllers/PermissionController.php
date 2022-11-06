<?php

namespace App\Http\Controllers;


use App\Models\Permission;
use App\Models\Role;

class PermissionController extends Controller
{
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index()
    {
    	return response()->json([
			'producer' => Permission::where('role_id', Role::ROLE_PRODUCER['id'])
				->get(),
			'advertiser' => [],
			'guarantor' => []
		]);
    }
}
