<?php

namespace App\Models;

use Laratrust\Models\LaratrustPermission;


/**
 * App\Models\Permission
 *
 * @property int $id
 * @property int $role_id
 * @property string $name
 * @property string|null $display_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Role|null $relatedRoles
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
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Permission extends LaratrustPermission
{
    public $guarded = [];

	protected $hidden = [
		'pivot'
	];

	/** producer permissions */
	const PERMISSIONS_PRODUCER = [
		self::PERMISSION_PRODUCER_REQUESTS,
		self::PERMISSION_PRODUCER_PERMISSIONS,
		self::PERMISSION_PRODUCER_PRODUCT,
		self::PERMISSION_PRODUCER_LOGO
	];

	const PERMISSION_PRODUCER_REQUESTS = [
		'name' => 'producer_requests',
		'display_name' => 'Управление запросами',
		'description' => ''
	];
	const PERMISSION_PRODUCER_PERMISSIONS = [
		'name' => 'producer_permissions',
		'display_name' => 'Управление разрешениями',
		'description' => ''
	];
	const PERMISSION_PRODUCER_PRODUCT = [
		'name' => 'producer_product',
		'display_name' => 'Управление продуктом',
		'description' => ''
	];
	const PERMISSION_PRODUCER_LOGO = [
		'name' => 'producer_logo',
		'display_name' => 'Изменение логотипа',
		'description' => ''
	];
	/** end producer permissions */

	/** use this instead of laratrust's 'roles' */
	public function relatedRoles()
	{
		return $this->belongsTo(Role::class);
	}
}
