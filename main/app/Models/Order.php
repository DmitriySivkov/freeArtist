<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $transaction_id
 * @property int|null $user_id
 * @property int $producer_id
 * @property array $order_products
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Producer|null $producer
 * @property-read \App\Models\Transaction|null $transaction
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereProducerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory, HasJsonRelationships;

    const ORDER_STATUS_NEW = 1;
	const ORDER_STATUS_PROCESS = 2;
	const ORDER_STATUS_CANCEL = 3;
	const ORDER_STATUS_DONE = 4;

    protected $guarded = [];

    protected $casts = [
    	'order_products' => 'json'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
    {
        /** for some reason laravel does not see user_id as a default foreign key.
         * So its specified.
         */
        return $this->belongsTo(User::class, 'user_id');
    }

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
	public function products()
	{
		return $this->belongsToJson(Product::class, 'order_products[]->id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function transaction()
	{
		return $this->belongsTo(Transaction::class);
	}
}
