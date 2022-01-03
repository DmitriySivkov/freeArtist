<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
{
    public function index()
    {
      $roles = Cache::remember('roles', 60, function() {
        return Role::all();
      });
      return response()->json([
        'roles' => $roles
      ]);
    }
}
