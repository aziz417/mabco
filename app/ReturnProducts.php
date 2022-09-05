<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnProducts extends Model
{
    protected $fillable = [
        'seller_id',
        'return_code',
        'retailer_id',
        'type',
        'approve',
        'total_amount',
        'commission_type',
        'commission_value',
        'discount',
        'return_amount',
        'date',
        'order_id',
    ];
}
