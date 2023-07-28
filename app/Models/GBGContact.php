<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GBGContact extends Model
{
    protected $table = 'contacts';
    protected $connection = "mysqlgbg";
    protected $guarded = [];
}
