<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReasonForAttending extends Model
{
    protected $table = 'reason_for_attending';

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
