<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $with = ['customer', 'producer'];

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

}
