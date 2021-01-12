<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobLevel extends Model
{
    protected $table = 'job_level';

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
