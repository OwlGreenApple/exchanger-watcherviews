<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;

    /*
      coins_value = total coins per 1000 views ex : 180 sec => 1000,000 coins = 1000 views
    */

    protected $table = "exchanges";
}
