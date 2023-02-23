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

	protected $hidden = ['pivot'];

    protected $with = ['from', 'to'];

	protected $appends = ['status'];

	const STATUS_PENDING = [
		'id' => 1,
		'label' => 'На рассмотрении'
	];
	const STATUS_ACCEPTED = [
		'id' => 2,
		'label' => 'Принято'
	];
	const STATUS_REJECTED_BY_RECIPIENT = [
		'id' => 3,
		'label' => 'Отказано получателем'
	];
	const STATUS_REJECTED_BY_CONTRIBUTOR = [
		'id' => 4,
		'label' => 'Отменено заявителем'
	];

	const STATUSES = [
		self::STATUS_PENDING,
		self::STATUS_ACCEPTED,
		self::STATUS_REJECTED_BY_RECIPIENT,
		self::STATUS_REJECTED_BY_CONTRIBUTOR
	];

	/**
	 * Get the channels that model events should broadcast on.
	 *
	 * @param  string  $event
	 * @return \Illuminate\Broadcasting\Channel|array
	 */
	public function broadcastOn($event)
	{
		$broadcastOn = [];
		if (is_a($this->from, User::class) || is_a($this->to, User::class))
			$broadcastOn[] = new PrivateChannel(
				'relation-requests.user.' . ( is_a($this->from, User::class) ? $this->from->id : $this->to->id )
			);

		if (!is_a($this->to, User::class))
			$broadcastOn[] = new PrivateChannel(
				'relation-requests.team.' . $this->to->team->id
			);

		return $broadcastOn;
	}

	/**
	 * Get the data to broadcast for the model.
	 *
	 * @param  string  $event
	 * @return array
	 */
	public function broadcastWith($event)
	{
		if (is_a($this->from, User::class) && !is_a($this->to, User::class))
			return [
				'model' => $this,
				'role' => Role::where('name', Role::ROLE_PRODUCER['name'])->first(),
				'team' => $this->to->team
			];

		return ['model' => $this];
	}

	/**
	 * Get the queue that should be used to broadcast model events.
	 *
	 * @return string|null
	 */
	public function broadcastQueue()
	{
		return config('queue.broadcast');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Casts\Attribute
	 */
	protected function status(): Attribute
	{
		return Attribute::make(
			get: fn ($statusId) => collect([
				'id' => $statusId,
				'label' => collect(self::STATUSES)
					->filter(fn($status) => $status['id'] === $statusId)
					->first()['label']
			]),
        );
    }

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
