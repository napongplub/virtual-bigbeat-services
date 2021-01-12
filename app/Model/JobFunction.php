<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobFunction extends Model
{
    protected $table = 'job_function';

    protected $fillable = [
        "name_en",
        "name_th",
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
