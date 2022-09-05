<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'stock_id',
                        'user_id',
                        'product_name',
                        'category_name',
                        'brand_name',
                        'unit_name',
                        'old_quantity',
                        'add_or_less',
                        'now_quantity',
                        'type',
                        'price',
                        'amount',
    ];
}
