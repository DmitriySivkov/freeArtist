<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Product extends Model
{
    use HasFactory, HasJsonRelationships;

    protected $casts = [
    	'composition' => 'array'
	];

    public function producer()
	{
		return $this->belongsTo(Producer::class);
	}
}
