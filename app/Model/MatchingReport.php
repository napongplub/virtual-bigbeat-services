<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MatchingReport extends Model
{
    protected $table = 'matching_report';

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
}
