<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\City
 *
 * @property int $id
 * @property string|null $address
 * @property string|null $postal_code
 * @property string|null $country
 * @property string|null $federal_district
 * @property string|null $region_type
 * @property string|null $region
 * @property string|null $area_type
 * @property string|null $area
 * @property string|null $city_type
 * @property string|null $city
 * @property string|null $settlement_type
 * @property string|null $settlement
 * @property string|null $kladr_id
 * @property string|null $fias_id
 * @property int|null $fias_level
 * @property int|null $capital_marker
 * @property string|null $okato
 * @property string|null $oktmo
 * @property string|null $tax_office
 * @property string|null $timezone
 * @property string|null $lat
 * @property string|null $lon
 * @property int|null $population
 * @property int|null $foundation_year
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Producer[] $producers
 * @property-read int|null $producers_count
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereAreaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCapitalMarker($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereFederalDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereFiasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereFiasLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereFoundationYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereKladrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereOkato($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereOktmo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City wherePopulation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereRegionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereSettlement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereSettlementType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereTaxOffice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereTimezone($value)
 * @mixin \Eloquent
 */
class City extends Model
{
    use HasFactory;

	protected $guarded = [];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function producers()
	{
		return $this->hasMany(Producer::class);
	}
}
