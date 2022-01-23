<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function producer()
    {
        return $this->belongsTo(Producer::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class);
    }
}
