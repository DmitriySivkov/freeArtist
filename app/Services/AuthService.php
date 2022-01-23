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

        $token = $user->createToken('web-app')->accessToken;
        /**
         * mandatory for cross-site: SSL; headers: secure=true;sameSite=none
         */
        $authCookie = cookie('token', $token, 0,
            null, null, true, true, false, 'none');

        return response($user->load(['role']))->withCookie($authCookie);
    }

    public function loginWithToken(Request $request)
    {
        if (!$request->hasHeader('Authorization') || $request->header('Authorization') !== 'Bearer ' . $request->cookie('token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->cookie('token'));
        }

        /** @var User $user */
        $user = auth('api')->user();

        return response($user->load(['role']));
    }
}
