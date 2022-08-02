<?php


namespace App\Services;

use App\Models\Producer;
use App\Models\RelationRequest;
use App\Models\User;
use App\Services\RelationRequests\UserRelationRequestService;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
	/**
	 * @param $credentials
	 * @return \Illuminate\Http\JsonResponse
	 */
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

        return response()->json($user->load($this->getUserLoadRelations()))
			->withCookie(
				cookie('token', $token, 0, null, null, true, true, false, 'none')
			);
    }

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function loginWithToken(Request $request)
    {
        if (!$request->hasHeader('Authorization') || $request->header('Authorization') !== 'Bearer ' . $request->cookie('token'))
            $request->headers->set('Authorization', 'Bearer ' . $request->cookie('token'));

        /** @var User $user */
        $user = auth("sanctum")->user();

		$user->requests = (new UserRelationRequestService)->getOutgoingCoworkingRequests();
		// WORK WITH COMMITS ? SORT OUT BY WHOM TO WHOM

        return response()->json($user->load($this->getUserLoadRelations()));
    }

	/**
	 * @return array
	 */
	private function getUserLoadRelations()
	{
		return [
			'roles',
			'teams',
			'permissions',
//			'outgoingCoworkingRequests' => function($query) {
//				$query->where('status', '!=', RelationRequest::STATUS_ACCEPTED['id'])
//			},
//			'producers.outgoingProducerPartnershipRequests' => function($query) {
//				$query->where('status', '!=', RelationRequest::STATUS_ACCEPTED['id']);
//			},
//			'producers.incomingProducerPartnershipRequests' => function($query) {
//				$query->where('status', '!=', RelationRequest::STATUS_ACCEPTED['id']);
//			},
//			'producers.incomingCoworkingRequests' => function($query) {
//				$query->where('status', '!=', RelationRequest::STATUS_ACCEPTED['id']);
//			},
		];
	}
}
