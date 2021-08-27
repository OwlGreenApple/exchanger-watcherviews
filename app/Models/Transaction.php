<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    /*
		type : 
		1 -- buy
		2 -- sell
		3 -- withdraw coin

		status : 
		hanya untuk user tipe 'BUY'
		0 -- transaksi sedang berjalan
		1 -- transaksi sudah selesai

		hanya untuk user tipe 'SELL'

		0 -- belum ada transaksi
		1 -- transaksi sedang berjalan
		2 -- transaksi sudah selesai
    */
}
