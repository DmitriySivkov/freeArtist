<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelationRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static Builder|RelationRequest coworkingRequests()
 * @method static Builder|RelationRequest partnershipRequests()
 */
class RelationRequest extends Model
{
    use HasFactory;

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
	 * @return \Illuminate\Database\Eloquent\Casts\Attribute
	 */
	protected function status(): Attribute
	{
		return new Attribute(
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
		return $this->belongsTo(User::class, 'from', 'id');
	}

	public function to()
	{
		return $this->belongsTo(User::class, 'to', 'id');
	}

	public function scopeCoworkingRequests(Builder $query)
	{
		return $query->where('type', self::TYPE_COWORKING);
	}

	public function scopePartnershipRequests(Builder $query)
	{
		return $query->where('type', self::TYPE_PRODUCER_PARTNERSHIP);
	}

}
