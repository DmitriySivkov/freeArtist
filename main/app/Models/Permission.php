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

	const PERMISSION_OWNER = [
		'name' => 'owner',
		'display_name' => 'Полные права владельца',
		'description' => ''
	];

	/** producer permissions */
	const PERMISSIONS_PRODUCER = [
		self::PERMISSION_PRODUCER_OUTGOING_PARTNERSHIP_REQUESTS,
		self::PERMISSION_PRODUCER_INCOMING_PARTNERSHIP_REQUESTS,
		self::PERMISSION_PRODUCER_INCOMING_COWORKING_REQUESTS
	];

	const PERMISSION_PRODUCER_OUTGOING_PARTNERSHIP_REQUESTS = [
		'name' => 'producer_outgoing_partnership_requests',
		'display_name' => 'Управление исходящими запросами на партнерство между изготовителями',
		'description' => ''
	];
	const PERMISSION_PRODUCER_INCOMING_PARTNERSHIP_REQUESTS = [
		'name' => 'producer_incoming_producer_partnership_requests',
		'display_name' => 'Управление входящими запросами на партнерство между изготовителями',
		'description' => ''
	];
	const PERMISSION_PRODUCER_INCOMING_COWORKING_REQUESTS = [
		'name' => 'producer_incoming_coworking_requests',
		'display_name' => 'Управление входящими запросами на сотрудничество между изготовителем и пользователем',
		'description' => ''
	];
	/** end producer permissions */
}
