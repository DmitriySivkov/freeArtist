<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Producer
 *
 * @property int $id
 * @property string|null $logo_id
 * @property int|null $city_id
 * @property int|null $storefront_image_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RelationRequest[] $incomingRelationRequests
 * @property-read int|null $incoming_relation_requests_count
 * @property-read \App\Models\Image|null $logo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RelationRequest[] $outgoingRelationRequests
 * @property-read int|null $outgoing_relation_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PaymentMethod[] $paymentMethods
 * @property-read int|null $payment_methods_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @property-read \App\Models\Image|null $storefrontImage
 * @property-read \App\Models\Team|null $team
 * @method static \Illuminate\Database\Eloquent\Builder|Producer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Producer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Producer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Producer whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Producer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Producer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Producer whereLogoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Producer whereStorefrontImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Producer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Producer extends Model
{
	protected $guarded = [];

	protected $hidden = ['pivot'];

	protected $casts = [
		'storefront' => 'array'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function logo()
	{
		return $this->belongsTo(Image::class, 'logo_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function storefrontImage()
	{
		return $this->belongsTo(Image::class, 'storefront_image_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function orders()
    {
        return $this->hasMany(Order::class);
    }

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphOne
	 */
	public function team()
	{
		return $this->morphOne(Team::class, 'detailed');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function products()
	{
		return $this->hasMany(Product::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function incomingRelationRequests()
	{
		return $this->morphMany(RelationRequest::class, 'to');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function outgoingRelationRequests()
	{
		return $this->morphMany(RelationRequest::class, 'from');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function city()
	{
		return $this->belongsTo(City::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function paymentMethods()
	{
		return $this->belongsToMany(PaymentMethod::class);
	}
}
