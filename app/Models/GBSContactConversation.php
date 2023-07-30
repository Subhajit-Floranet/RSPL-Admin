<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GBSContactConversation extends Model
{
    protected $table = 'contact_conversations';
    protected $connection = "mysqlgbs";
    protected $guarded = [];
}
