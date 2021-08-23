<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    /*
		status :
		- 0 == order still not confirmed by user
		- 1 == order has confirmed by user already
		- 2 == order has confirmed by admin
    */

    protected $table = 'orders';
}
