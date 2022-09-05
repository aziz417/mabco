<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class SalesPersion extends Model
{
    use HasRoles;

    protected $guarded = [];
    protected $guard_name = 'web';
}
