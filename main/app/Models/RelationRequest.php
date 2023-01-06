<?php

namespace App\Models;


use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Builder;
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
 * @method static Builder|RelationRequest newModelQuery()
 * @method static Builder|RelationRequest newQuery()
 * @method static Builder|RelationRequest query()
 * @method static Builder|RelationRequest whereCreatedAt($value)
 * @method static Builder|RelationRequest whereFromId($value)
 * @method static Builder|RelationRequest whereFromType($value)
 * @method static Builder|RelationRequest whereId($value)
 * @method static Builder|RelationRequest whereMessage($value)
 * @method static Builder|RelationRequest whereStatus($value)
 * @method static Builder|RelationRequest whereToId($value)
 * @method static Builder|RelationRequest whereToType($value)
 * @method static Builder|RelationRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RelationRequest extends Model
{
    use BroadcastsEvents, HasFactory;

    const TYPE_COWORKING = 1;
	const TYPE_PRODUCER_PARTNERSHIP = 2;

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

    protected $guarded = [];

    protected $with = ['from', 'to'];

	protected $appends = ['status'];

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

		if (is_a($this->to, Producer::class))
			$broadcastOn[] = new PrivateChannel(
				'relation-requests.incoming.producer.' . $this->to->id
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
		if (is_a($this->from, User::class) && is_a($this->to, Producer::class))
			return [
				'model' => $this,
				'type' => self::TYPE_COWORKING,
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

	public function from()
	{
		return $this->morphTo();
	}

	public function to()
	{
		return $this->morphTo();
	}
}
