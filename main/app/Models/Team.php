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
 * @property int $user_id
 * @property int $detailed_id
 * @property string $detailed_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $detailed
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDetailedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDetailedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUserId($value)
 * @mixin \Eloquent
 */
class Team extends LaratrustTeam
{
    public $guarded = [];

	protected $hidden = ['pivot'];

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

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function users()
	{
		return $this->belongsToMany(
			User::class,
			'role_user',
			'team_id',
			'user_id'
		);
	}

}
