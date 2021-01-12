<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ExhibitorLogin extends Model
{
    protected $table ='exhibitor_login_log';

    protected $fillable = [
        'exhibitor_id',
        'ip',
        'user_agent',
        'success'
    ];
}
