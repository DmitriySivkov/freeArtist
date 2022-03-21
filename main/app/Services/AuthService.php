<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Http\Request;

class AuthService
{
    public function loginWithCredentials($credentials)
    {
        if (!auth()->attempt($credentials))
            return response()->json(['errors' => ['total' => ['Неверные почта или пароль']]], 422);

        /** @var User $user */
        $user = auth()->user();

        return response()->json($user->load('role', 'producer'));
    }

    public function loginWithToken(Request $request)
    {
        if (!$request->hasHeader('Authorization') || $request->header('Authorization') !== 'Bearer ' . $request->cookie('token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->cookie('token'));
        }

        /** @var User $user */
        $user = auth('api')->user();

        return response()->json($user->load('role', 'producer'));
    }
}
