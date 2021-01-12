<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LogJoin extends Model
{
    protected $table = 'log_join_matching';

    protected $fillable = [
        "id",
        "join_id",
        "role",
        "meeting_id",
        "meeting_code",
        "meeting_status",
        "appointment_data",
        "slot_data",

    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
