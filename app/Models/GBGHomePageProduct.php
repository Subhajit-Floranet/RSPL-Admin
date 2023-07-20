<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GBGHomePageProduct extends Model
{
    protected $table = 'home_page_products';
    protected $connection = "mysqlgbg";
    protected $guarded = [];
    public $timestamps = false;
}
