<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NatureOfBusiness extends Model
{
    protected $table = 'nature_of_business';
 

    protected $fillable = [
        "name_en",
        "name_th",
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
