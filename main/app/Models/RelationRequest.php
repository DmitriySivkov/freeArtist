<?php

namespace App\Models;


use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RelationRequest
 *
 * @property int $id
 * @property int $from_id
 * @property string $from_type
 * @property int $to_id
 * @property string $to_type
 * @property int $status
 * @property string|null $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $from
 * @property-read Model|\Eloquent $to
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereFromType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereToType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RelationRequest extends Model
{
    use BroadcastsEvents, HasFactory;

    protected $guarded = [];

    protected $with = ['from', 'to'];

	const STATUS_PENDING = 1;
	const STATUS_ACCEPTED = 2;
	const STATUS_REJECTED_BY_RECIPIENT = 3;
	const STATUS_CANCELED_BY_CONTRIBUTOR = 4;

	const STATUSES = [
		self::STATUS_PENDING => 'На рассмотрении',
		self::STATUS_ACCEPTED => 'Принято',
		self::STATUS_REJECTED_BY_RECIPIENT => 'Отказано получателем',
		self::STATUS_CANCELED_BY_CONTRIBUTOR => 'Отмена'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function from()
	{
		return $this->morphTo();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function to()
	{
		return $this->morphTo();
	}
}
