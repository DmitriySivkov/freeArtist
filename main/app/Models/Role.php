<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property string|null $display_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends LaratrustRole
{
    public $guarded = [];

	protected $hidden = ['pivot'];

	const ROLES = [
		self::ROLE_PRODUCER,
		self::ROLE_ADVERTISER,
		self::ROLE_GUARANTOR,
	];

	const ROLE_PRODUCER = [
		'id' => 1,
		'name' => 'producer',
		'display_name' => 'Изготовитель',
		'description' => ''
	];

	const ROLE_ADVERTISER = [
		'id' => 2,
		'name' => 'advertiser',
		'display_name' => 'Рекламодатель',
		'description' => ''
	];

	const ROLE_GUARANTOR = [
		'id' => 3,
		'name' => 'guarantor',
		'display_name' => 'Гарант',
		'description' => ''
	];



}
