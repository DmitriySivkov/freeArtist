<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

	const CUSTOMER = 1;
	const PRODUCER = 2;

	const ROLES = [
		self::CUSTOMER => 'customer',
		self::PRODUCER => 'producer'
	];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
