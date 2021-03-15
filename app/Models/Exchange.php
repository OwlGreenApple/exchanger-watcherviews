<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;

    /*
      coins_value = total coins per 1000 views ex : 180 sec => 1000,000 coins = 1000 views

      refill :
      0 = user doesn't use refill feature
      1 = user does using manual refill
      2 = user does using automatic refill

      refill_btn :
      0 = button refill not appear
      1 = button refill appear

      refill_api :
      0 = still not get sign from watcherview, so check refill wouldn't be run.
      1 = has get sign from watcherview, so check refill would be run.
    */

    protected $table = "exchanges";
}
