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
		- 3 == order has cancelled by admin
		- 4 == order has cancelled by system
		- 6 == order get flagged after 6 hours -- to avoid system send WA after next 6 hours
    */

    protected $table = 'orders';
}
