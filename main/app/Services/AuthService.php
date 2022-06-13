<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    public function loginWithCredentials($credentials)
    {
        if (!auth()->attempt($credentials))
            return response()->json(['errors' => ['total' => ['Неверный телефон или пароль']]], 422);

        /** @var User $user */
        $user = auth()->user();
        $abilities = ['*'];

		$loginToken = PersonalAccessToken::query()->where('tokenable_id', $user->id)
			->where('tokenable_type', User::class)->first();

		if ($loginToken) {
			$abilities = $loginToken->abilities;
			$loginToken->delete();
		}

		$token = $user->createToken($user->phone, $abilities)
			->plainTextToken;

        return response()->json($user->load(
        	[
        		'roles',
				'producers',
				'incomingCoworkingProducerRequests',
				'outgoingCoworkingProducerRequests'
			]
		))
			->withCookie(cookie('token', $token, 0, null, null, true, true, false, 'none'));
    }

    public function loginWithToken(Request $request)
    {
        if (!$request->hasHeader('Authorization') || $request->header('Authorization') !== 'Bearer ' . $request->cookie('token'))
            $request->headers->set('Authorization', 'Bearer ' . $request->cookie('token'));

        /** @var User $user */
        $user = auth("sanctum")->user();

        return response()->json($user->load(
        	[
        		'roles',
				'producers',
				'incomingCoworkingProducerRequests',
				'outgoingCoworkingProducerRequests'
			]
		));
    }
}
