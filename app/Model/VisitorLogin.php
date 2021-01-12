<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class VisitorLogin extends Model
{
    protected $table = "visitor_login_log";

    protected $fillable = [
        'visitor_id',
        'ip',
        'user_agent',
        'success'
    ];
}
