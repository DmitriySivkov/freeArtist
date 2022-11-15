<?php

namespace App\Services\Producers;

use App\Models\Permission;
use App\Models\Producer;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProducerService
{
	public function setLogo(Producer $producer)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_MANAGE_LOGO['name'], $producer->team) &&
			!$user->owns($producer->team)
		)
			throw new \LogicException('Доступ закрыт');

		$basePath = 'team_' . $producer->team->id . '/logo';

		if (Storage::disk('public')->exists($basePath))
			Storage::disk('public')->deleteDirectory($basePath);

		$path = Storage::disk('public')->putFile(
			$basePath,
			request()->file('logo'),
		);

		if (!$path)
			throw new \LogicException('Ошибка сервиса');

		$producer->logo = $path;
		$producer->save();

		return $path;
	}
}
