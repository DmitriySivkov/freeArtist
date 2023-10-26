<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PaymentMethod
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Producer[] $producers
 * @property-read int|null $producers_count
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PaymentMethod extends Model
{
    use HasFactory;

	public $guarded = [];

	const PAYMENT_METHODS = [
		self::PAYMENT_METHOD_CARD_ID 	=> self::PAYMENT_METHOD_CARD,
		self::PAYMENT_METHOD_SBP_ID 	=> self::PAYMENT_METHOD_SBP,
		self::PAYMENT_METHOD_CASH_ID 	=> self::PAYMENT_METHOD_CASH,
		self::PAYMENT_METHOD_QIWI_ID 	=> self::PAYMENT_METHOD_QIWI,
	];

	const PAYMENT_METHOD_CARD 	= 'Оплата картой';
	const PAYMENT_METHOD_SBP 	= 'Оплата через СБП';
	const PAYMENT_METHOD_CASH 	= 'Оплата наличными';
	const PAYMENT_METHOD_QIWI 	= 'Оплата с помощью QIWI';

	const PAYMENT_METHOD_CARD_ID 	= 1;
	const PAYMENT_METHOD_SBP_ID 	= 2;
	const PAYMENT_METHOD_CASH_ID 	= 3;
	const PAYMENT_METHOD_QIWI_ID 	= 4;

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function producers()
	{
		return $this->belongsToMany(Producer::class);
	}
}
