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

	/** producer permissions */
	const PERMISSIONS_PRODUCER = [
		self::PERMISSION_PRODUCER_OUTGOING_REQUESTS,
		self::PERMISSION_PRODUCER_INCOMING_REQUESTS,
		self::PERMISSION_PRODUCER_MANAGE_PERMISSIONS,
		self::PERMISSION_PRODUCER_MANAGE_PRODUCT,
		self::PERMISSION_PRODUCER_CREATE_PRODUCT,
		self::PERMISSION_PRODUCER_DELETE_PRODUCT,
		self::PERMISSION_PRODUCER_MANAGE_LOGO
	];

	const PERMISSION_PRODUCER_OUTGOING_REQUESTS = [
		'name' => 'producer_outgoing_requests',
		'display_name' => 'Управление исходящими запросами',
		'description' => ''
	];
	const PERMISSION_PRODUCER_INCOMING_REQUESTS = [
		'name' => 'producer_incoming_requests',
		'display_name' => 'Управление входящими запросами',
		'description' => ''
	];
	const PERMISSION_PRODUCER_MANAGE_PERMISSIONS = [
		'name' => 'producer_manage_permissions',
		'display_name' => 'Управление разрешениями изготовителя',
		'description' => ''
	];
	const PERMISSION_PRODUCER_MANAGE_PRODUCT = [
		'name' => 'producer_manage_product',
		'display_name' => 'Управление продуктом',
		'description' => ''
	];
	const PERMISSION_PRODUCER_CREATE_PRODUCT = [
		'name' => 'producer_create_product',
		'display_name' => 'Создание продукта',
		'description' => ''
	];
	const PERMISSION_PRODUCER_DELETE_PRODUCT = [
		'name' => 'producer_delete_product',
		'display_name' => 'Удаление продукта',
		'description' => ''
	];
	const PERMISSION_PRODUCER_MANAGE_LOGO = [
		'name' => 'producer_manage_logo',
		'display_name' => 'Изменение логотипа изготовителя',
		'description' => ''
	];
	/** end producer permissions */

	/** use this instead of laratrust's 'roles' */
	public function relatedRoles()
	{
		return $this->belongsTo(Role::class);
	}
}
