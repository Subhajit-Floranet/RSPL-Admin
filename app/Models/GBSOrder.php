<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GBSOrder extends Model
{
    protected $table = 'orders';
    protected $connection = "mysqlgbs";
    protected $guarded = [];
}
