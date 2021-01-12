<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LogBrochure extends Model
{
    protected $table = 'log_brochure';
    
    protected $fillable = [
        "ref_id",
        "owner_id",
        "actor_id", 
        "actor_type", 
        "action", 
        "data", 
        "description"
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
