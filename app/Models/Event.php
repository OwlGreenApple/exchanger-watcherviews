<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    /*
		type : 
		0 -- warning admin
		1 -- transaction

		is read : 
		0 -- blm dibaca
		1 -- sdh dibaca
    */
}
