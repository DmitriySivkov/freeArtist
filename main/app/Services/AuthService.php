<?php


namespace App\Services;


use App\Models\Producer;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Services\RelationRequests\ProducerRelationRequestService;
use App\Services\RelationRequests\UserRelationRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
	protected ?User $user;
	protected Collection $userTeams;

	/**
	 * @param $credentials
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function loginWithCredentials($credentials)
    {
        if (!auth()->attempt($credentials))
            return response()->json(['errors' => ['total' => ['Неверный телефон или пароль']]], 422);

        $this->user = auth()->user();
        $abilities = ['*'];

		$loginToken = PersonalAccessToken::query()->where('tokenable_id', $this->user->id)
			->where('tokenable_type', User::class)->first();

		if ($loginToken) {
			$abilities = $loginToken->abilities;
			$loginToken->delete();
		}

		$token = $this->user->createToken($this->user->phone, $abilities)
			->plainTextToken;

		$this->getUserPayload();

        return response()->json([
			'user' => $this->user,
			'user_producer' => $this->getUserProducerTeams()
		])
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
        if (
			!$request->hasHeader('Authorization') ||
			$request->header('Authorization') !== 'Bearer ' . $request->cookie('token')
		)
            $request->headers->set('Authorization', 'Bearer ' . $request->cookie('token'));

        $this->user = auth("sanctum")->user();

		// todo - setup api cache settings. For now - "php artisan optimize" if backend is changed
//		if (!$this->user) {
//			$request->headers->remove('Authorization');
//			$request->cookies->remove('token');
//			return false;
//		}

		$this->getUserPayload();

        return response()->json([
			'user' => $this->user,
			'user_producer' => $this->getUserProducerTeams()
		]);
    }

	/**
	 * @return void
	 */
	private function getUserPayload()
	{
		$this->user->load([
			'roles',
		]);

		$urrService = new UserRelationRequestService;
		$urrService->setUser($this->user);
		$this->user->outgoing_coworking_requests = $urrService->getOutgoingCoworkingRequests();
	}

	/**
	 * @return array|Collection
	 */
	private function getUserProducerTeams()
	{
		$userProducerTeams = $this->user->rolesTeams()
			->where('role_id', Role::ROLE_PRODUCER['id'])
			->get();

		if ($userProducerTeams->isEmpty())
			return [];

		$prrService = new ProducerRelationRequestService;
		return $userProducerTeams->map(function(Team $team) use ($prrService) {
			$prrService->setProducer($team->detailed);

			if (
				$this->user->owns($team) ||
				$this->user->hasPermission("producer_incoming_coworking_requests", $team->name)
			) {
				$requests = [
					'data' => [
						'incoming_coworking_requests' => $prrService->getIncomingCoworkingRequests()
					],
				];
				$requests['total_pending_request_count'] = collect($requests['data'])
					->reduce(function($carry, $requestList) {
						$carry += count($requestList);
						return $carry;
					}, 0);

				$team->requests = $requests;
			}
			return $team;
		});
	}

}
