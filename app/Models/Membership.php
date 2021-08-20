<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    /*
    	every user order when their package still valid or running, will be save here
		status :
		0 --> membership wait until activation date - start
		1 --> membership activated
    */

    protected $table = 'memberships';
}
