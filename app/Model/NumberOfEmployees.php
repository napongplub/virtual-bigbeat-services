<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NumberOfEmployees extends Model
{
    protected $table = 'number_of_employees';

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
