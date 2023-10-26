<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

/**
 * App\Models\ProducerOrderPriority
 *
 * @property int $id
 * @property int $producer_id
 * @property int $status
 * @property array $order_priority
 * @property-read \App\Models\Producer|null $producer
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerOrderPriority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerOrderPriority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerOrderPriority query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerOrderPriority whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerOrderPriority whereOrderPriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerOrderPriority whereProducerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerOrderPriority whereStatus($value)
 * @mixin \Eloquent
 */
class ProducerOrderPriority extends Model
{
    use HasFactory, HasJsonRelationships;

	// todo - limit amount of stored orders under every status
	protected $table = 'producer_order_priority';

	protected $guarded = [];

	public $timestamps = false;

	protected $casts = [
		'order_priority' => 'json'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function producer()
	{
		return $this->belongsTo(Producer::class);
	}

	/**
	 * @return \Staudenmeir\EloquentJsonRelations\Relations\BelongsToJson
	 */
	public function orders()
	{
		return $this->belongsToJson(Order::class, 'order_priority');
	}
}
