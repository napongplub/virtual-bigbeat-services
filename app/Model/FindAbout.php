<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FindAbout extends Model
{
    protected $table = 'find_about_bct';

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
