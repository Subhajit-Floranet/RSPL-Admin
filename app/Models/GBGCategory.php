<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GBGCategory extends Model
{
    protected $table = 'categories';
    protected $connection = "mysqlgbg";
    protected $guarded = [];

    
}
