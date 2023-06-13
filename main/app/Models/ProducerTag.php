<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProducerTag
 *
 * @property int $id
 * @property string $name
 * @property int $producer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Producer|null $producer
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerTag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerTag whereProducerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProducerTag extends Model
{
    use HasFactory;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function producer()
	{
		return $this->belongsTo(Producer::class);
	}
}
