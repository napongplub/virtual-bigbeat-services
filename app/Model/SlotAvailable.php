<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\SlotTime;


class SlotAvailable extends Model
{
    protected $table = 'matching_enable_slot';

    protected $fillable = [
        "id"
    ];
    
    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];

    public function slotTime(){
        return $this->hasOne(SlotTime::class, 'id', 'slot_time');

    }
}
