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

	const PERMISSIONS_PRODUCER = [
		self::PERMISSION_PRODUCER_ALL,
		self::PERMISSION_PRODUCER_RELATION_REQUESTS
	];

	const PERMISSION_PRODUCER_ALL = [
		'name' => 'permission_producer_all',
		'display_name' => 'Полные полномочия',
		'description' => ''
	];
	const PERMISSION_PRODUCER_RELATION_REQUESTS = [
		'name' => 'permission_producer_relation_requests',
		'display_name' => 'Управление запросами',
		'description' => ''
	];
}
