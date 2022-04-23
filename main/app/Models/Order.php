<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Order extends Model
{
    use HasFactory, HasJsonRelationships;

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
		return $this->belongsToJson(Product::class, 'products');
	}

}
