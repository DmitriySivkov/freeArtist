<?php

namespace App\Models;

use Laratrust\Models\LaratrustPermission;

/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $name
 * @property string|null $display_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Permission extends LaratrustPermission
{
    public $guarded = [];

	const PERMISSIONS = [
		self::PERMISSION_PRODUCER_OWNER,
		self::PERMISSION_PRODUCER_COWORKER
	];

	const PERMISSION_PRODUCER_OWNER = [
		'name' => 'producer_owner',
		'display_name' => 'Владелец изготовителя',
		'description' => 'Полномочия владельца изготовителя'
	];
	const PERMISSION_PRODUCER_COWORKER = [
		'name' => 'producer_coworker',
		'display_name' => 'Сотрудник изготовителя',
		'description' => 'Полномочия сотрудника изготовителя'
	];
}
