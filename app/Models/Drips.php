<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drips extends Model
{
    use HasFactory;

    /*
      status = 0 ==== Still on queue
      status = 1 ==== Has put into API
    */

    protected $table = 'drips';
}
