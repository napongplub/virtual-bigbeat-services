<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SlotTime extends Model
{
    protected $table = 'matching_slot';

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
