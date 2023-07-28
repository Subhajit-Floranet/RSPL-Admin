<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GBSContact extends Model
{
    protected $table = 'contacts';
    protected $connection = "mysqlgbs";
    protected $guarded = [];
}
