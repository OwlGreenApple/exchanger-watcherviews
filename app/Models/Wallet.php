<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'wallets';

    /*
		type :
		 1 = Withdraw / Penarikan coin
		 2 = Send / kirim coin
    */
}
