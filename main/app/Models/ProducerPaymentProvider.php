<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProducerPaymentProvider
 *
 * @property int $id
 * @property int $producer_id
 * @property int $payment_provider_id
 * @property mixed $payment_provider_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Producer|null $provider
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerPaymentProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerPaymentProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerPaymentProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerPaymentProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerPaymentProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerPaymentProvider wherePaymentProviderData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerPaymentProvider wherePaymentProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerPaymentProvider whereProducerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProducerPaymentProvider whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProducerPaymentProvider extends Model
{
    use HasFactory;

	protected $guarded = [];

	protected $casts = [
		'payment_provider_data' => 'json'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function provider()
	{
		return $this->belongsTo(Producer::class);
	}
}
