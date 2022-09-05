<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class Stock extends Model
{
    protected $fillable = [
        'product_id',
        'category_id',
        'brand_id',
        'unit_id',
        'quantity',
    ];

}
