<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property string $uuid
 * @property int $producer_id
 * @property array $order_data
 * @property int $payment_method
 * @property int|null $payment_provider_id
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order|null $order
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereOrderData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePaymentProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereProducerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUuid($value)
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    use HasFactory, HasJsonRelationships;

	protected $guarded = [];

	protected $casts = [
		'order_data' => 'json'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function order()
	{
		return $this->belongsTo(Order::class, 'uuid', 'transaction_uuid');
	}

	/**
	 * @return \Staudenmeir\EloquentJsonRelations\Relations\BelongsToJson
	 */
	public function products()
	{
		return $this->belongsToJson(Product::class, 'order_data[]->id');
	}
}
