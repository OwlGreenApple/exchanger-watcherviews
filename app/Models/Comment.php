<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /*
		parent_id == ini di pake kalo suatu saat diminta spy bisa buat reply comment
    */

    protected $table = "comments";
}
