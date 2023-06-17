<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductTag
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag query()
 * @mixin \Eloquent
 */
class ProductTag extends Model
{
    use HasFactory;

	protected $table = 'product_tag';
}
