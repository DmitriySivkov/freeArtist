<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;


class Product extends Model
{
    use HasFactory, HasJsonRelationships;

	protected $guarded = [];

	protected $hidden = ['pivot'];

	protected $casts = [
		'composition' => 'array',
		'price' => 'float'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function producer()
	{
		return $this->belongsTo(Producer::class);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function images()
	{
		return $this->morphMany(Image::class, 'imageable');
	}

}
