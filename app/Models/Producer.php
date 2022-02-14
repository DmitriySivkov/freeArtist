<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
		return $this->hasMany(User::class);
	}

	public function products()
	{
		return $this->hasMany(Product::class);
	}
}
