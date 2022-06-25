<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Producer
 *
 * @property int $id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\ProducerFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Producer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Producer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Producer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Producer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Producer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Producer whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Producer whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RelationRequest[] $joinRequests
 * @property-read int|null $join_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RelationRequest[] $joinInvitations
 * @property-read int|null $join_invitations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RelationRequest[] $incomingPartnershipProducerRequests
 * @property-read int|null $incoming_partnership_producer_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RelationRequest[] $outgoingPartnershipProducerRequests
 * @property-read int|null $outgoing_partnership_producer_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RelationRequest[] $incomingProducerPartnershipRequests
 * @property-read int|null $incoming_producer_partnership_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RelationRequest[] $outgoingProducerPartnershipRequests
 * @property-read int|null $outgoing_producer_partnership_requests_count
 */
class Producer extends Model
{
    use HasFactory;

    protected $guarded = [];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function orders()
    {
        return $this->hasMany(Order::class);
    }

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function users()
	{
		return $this->belongsToMany(User::class)
			->using(ProducerUser::class)
			->withPivot(['rights', 'user_active'])
			->withTimestamps();
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
	public function outgoingProducerPartnershipRequests()
	{
		return $this->morphMany(RelationRequest::class, 'from')
			->where('to_type', Producer::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function incomingProducerPartnershipRequests()
	{
		return $this->morphMany(RelationRequest::class, 'to')
			->where('from_type', Producer::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function incomingCoworkingRequests()
	{
		return $this->morphMany(RelationRequest::class, 'to')
			->where('from_type', User::class);
	}
}
