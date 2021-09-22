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
		2 -- pembeli sudah konfirmasi
		3 -- transaksi sudah selesai / pembeli menang dispute
		4 -- dispute
		5 -- penjual menang
		6 -- dispute ditutup user -- pembeli - penjual sama2 kena warning
    */
}
