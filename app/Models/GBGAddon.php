<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GBGAddon extends Model
{
    protected $table = 'products';
    protected $connection = "mysqlgbg";
    protected $guarded = [];
    // public $timestamps = false;
}
