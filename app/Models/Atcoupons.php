<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atcoupons extends Model
{
    use HasFactory;

    protected $table = "atcoupons";

    /*
		is_used :
		0 -- belum dipakai
		1 -- sdh dipakai

		type : 
		1 -- voucher Rp.100.000
		2 -- voucher Rp.200.000
    */
}
