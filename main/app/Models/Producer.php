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
 */
class Producer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function users()
	{
		return $this->belongsToMany(User::class)
			->withTimestamps();
	}

	public function products()
	{
		return $this->hasMany(Product::class);
	}
}
