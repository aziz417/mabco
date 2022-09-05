<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    protected $fillable = ['seller_id', 'retailer_id', 'adjustment_number', 'date', 'title', 'amount'];
}
