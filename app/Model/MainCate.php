<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MainCate extends Model
{
    protected $table = 'main_category';
    protected $fillable = [
        "name_en",
        "name_th",
    ];
    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
