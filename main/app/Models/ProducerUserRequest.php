<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProducerUserRequest
 *
 * @property int $id
 * @property int $from
 * @property int $to
 * @property int $type
 * @property int $status
 * @property string|null $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUserRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUserRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUserRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUserRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUserRequest whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUserRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUserRequest whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUserRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUserRequest whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUserRequest whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerUserRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProducerUserRequest extends Model
{
    use HasFactory;

    const TYPE_USER_TO_PRODUCER = 1;
	const TYPE_PRODUCER_TO_PRODUCER = 2;

	const STATUS_PENDING = 1;
	const STATUS_ACCEPTED = 2;
	const STATUS_REJECTED_BY_RECIPIENT = 3;
	const STATUS_REJECTED_BY_CONTRIBUTOR = 4;

    protected $guarded = [];
}
