<?php

namespace App\Models;

use Laratrust\Models\LaratrustTeam;

/**
 * App\Models\Team
 *
 * @property int $id
 * @property string $name
 * @property string|null $display_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Team extends LaratrustTeam
{
    public $guarded = [];

	protected $with = ['detailed'];

	const TEAMS = [
		self::TEAM_PRODUCER_OWNERS,
		self::TEAM_PRODUCER_COWORKERS
	];

	const TEAM_PRODUCER_OWNERS = [
		'name' => 'producer_owners',
		'display_name' => 'Владелец изготовителя',
		'description' => 'Полномочия владельца изготовителя'
	];
	const TEAM_PRODUCER_COWORKERS = [
		'name' => 'producer_coworkers',
		'display_name' => 'Сотрудник изготовителя',
		'description' => 'Полномочия сотрудника изготовителя'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function detailed()
	{
		return $this->morphTo();
	}
}
