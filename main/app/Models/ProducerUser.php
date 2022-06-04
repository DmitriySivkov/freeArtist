<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProducerUser
 *
 * @property int $id
 * @property int $producer_id
 * @property int $user_id
 * @property array|null $user_roles
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUser whereProducerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUser whereUserRoles($value)
 * @mixin \Eloquent
 * @property int $user_active
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUser whereUserActive($value)
 */
class ProducerUser extends Model
{
    use HasFactory;

    protected $table = 'producer_user';
}
