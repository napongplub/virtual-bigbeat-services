<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Exhibitor;
use App\Model\Register;
use App\Model\ExhibitorMatching;
use App\Model\RegisterMatching;
use App\Model\SlotAvailable;

class SlotAppointment extends Model
{
    protected $fillable = [
        'request_id',
        'request_type',
        'owner_id',
        'owner_type',

    ];
    protected $table = 'matching_appointment';

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];
    public function exhibitor()
    {
        return $this->hasMany(Exhibitor::class, 'id', 'owner_id');
    }

    public function register()
    {
        return $this->hasMany(Register::class, 'id', 'owner_id');
    }

    public function inComingRegister()
    {

        return $this->hasMany(RegisterMatching::class, 'id', 'request_id');
    }
    public function outGoingRegister()
    {

        return $this->hasMany(RegisterMatching::class, 'id', 'owner_id');
    }
    public function inComingExhibitor()
    {
            return $this->hasMany(ExhibitorMatching::class, 'id', 'request_id');
    }
    public function outGoingExhibitor()
    {
            return $this->hasMany(ExhibitorMatching::class, 'id', 'owner_id');
    }
    public function slotTime(){

        return $this->hasOne(SlotAvailable::class, 'id', 'slot_time');

    }
    public function requestTime(){

        return $this->hasOne(SlotAvailable::class, 'id', 'request_slot');

    }

}
