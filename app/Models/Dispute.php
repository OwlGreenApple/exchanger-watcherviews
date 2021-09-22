<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    protected $table = "disputes";

    /*
		role : 
		1 -- pembeli
		2 -- penjual
    */
}
