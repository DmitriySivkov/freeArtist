<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $casts = [
    	'composition' => 'array'
	];

    public function producer()
	{
		return $this->belongsTo(Producer::class);
	}
}
