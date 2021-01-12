<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LogCancel extends Model
{
    protected $table = 'log_cancel_matching';
    
    protected $fillable = [
        "id",
        "request_id",
        "role", 
        "type", 
        "appointment_data", 
        "slot_data", 
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
