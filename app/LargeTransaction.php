<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LargeTransaction extends Model
{
    protected $fillable = ['seller_id', 'retailer_id', 'transaction_number', 'date', 'amount'];
}
