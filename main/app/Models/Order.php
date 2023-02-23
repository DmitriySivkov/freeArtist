<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $user_id
 * @property int $producer_id
 * @property array $products
 * @property int $payment_method
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $customer
 * @property-read \App\Models\Producer|null $producer
 * @method static \Database\Factories\OrderFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereProducerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory, HasJsonRelationships;

    const ORDER_STATUS_NEW = 1;

    const ORDER_PAYMENT_METHOD_CARD = 1;
	const ORDER_PAYMENT_METHOD_CASH = 2;

    protected $guarded = [];

	protected $hidden = ['pivot'];

    protected $casts = [
    	'products' => 'json'
	];

    public function customer()
    {
        /** for some reason laravel does not see user_id as a default foreign key.
         * So its specified.
         */
        return $this->belongsTo(User::class, 'user_id');
    }

    public function producer()
    {
        return $this->belongsTo(Producer::class);
    }

    public function products()
	{
		return $this->belongsToJson(Product::class, 'products[]->product_id');
	}

}
