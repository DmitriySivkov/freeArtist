<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
{
    public function index()
    {
      $roles = Cache::remember('roles', 60, function() {
        return RoleModel::orderBy('id', 'asc')->get();
      });
      return response()->json([
        'roles' => $roles
      ]);
    }
}
