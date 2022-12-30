<?php

namespace App\Services;

use App\Contracts\ProducerServiceContract;
use App\Contracts\TeamServiceContract;
use App\Models\Producer;
use App\Models\Team;

class TeamService implements TeamServiceContract
{
	protected Team $team;

	/**
	 * @param Team $team
	 * @return void
	 */
	public function setTeam(Team $team)
	{
		$this->team = $team;
	}

	public function onAuth()
	{
		if ($this->team->detailed_type === Producer::class) {
			$service = app(ProducerServiceContract::class);
			$service->setProducer($this->team->detailed);
		}

		$data = $service->onAuth();

		foreach ($data as $prop => $value) {
			$this->team->setAttribute($prop, $value);
		}

		return $this->team;
	}
}
