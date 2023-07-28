<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GBGOrder extends Model
{
    protected $table = 'orders';
    protected $connection = "mysqlgbg";
    protected $guarded = [];
}
