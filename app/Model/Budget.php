<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'budget';

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
