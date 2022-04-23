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

		$token = $user->createToken($user->email)
			->plainTextToken;

        return response()->json($user->load(['role', 'producer']))
			->withCookie(cookie('token', $token, 0, null, null, true, true, false, 'none'));;
    }

    public function loginWithToken(Request $request)
    {
        if (!$request->hasHeader('Authorization') || $request->header('Authorization') !== 'Bearer ' . $request->cookie('token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->cookie('token'));
        }

        /** @var User $user */
        $user = auth("sanctum")->user();

        return response()->json($user->load(['role', 'producer']));
    }
}
