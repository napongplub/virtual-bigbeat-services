<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    protected $table = 'countries';

    protected $fillable = [
        "name",
        "code",
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
