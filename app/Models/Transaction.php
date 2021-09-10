<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    /*
		status : 
		0 -- belum ada transaksi
		1 -- transaksi sedang berjalan / deal
		2 -- transaksi sudah selesai
    */
}
