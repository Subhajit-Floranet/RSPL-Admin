<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GBGContactConversation extends Model
{
    protected $table = 'contact_conversations';
    protected $connection = "mysqlgbg";
    protected $guarded = [];
}
