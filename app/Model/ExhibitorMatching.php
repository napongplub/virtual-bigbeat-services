<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Model\Interest;
use App\Model\SubCate;
use App\Model\MainCate;
use App\Model\BrochureList;
use App\Model\Countries;
use App\Model\EposterList;
use App\Model\PromotionList;
use App\Model\VideoList;
use App\Model\SlotAppointment;
use App\Model\SlotAvailable;


use Storage;


class ExhibitorMatching extends Authenticatable implements JWTSubject
{
    protected $table    = "exhibitor_list";

    protected $fillable = [
        'name',
        'company',
        'position',
        'category',
        'website',
        'description',
        'logo',
        'facebook',
        'youtube',
        'twitter',
        'linkedin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email',
        'mobile',
        'address',
        'm_name',
        'm_email',
        'm_mobile',
        'welcome_msg',
        'offline_msg',
        'country_id'

    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];


    public function mainCate()
    {
        return $this->belongsToMany(MainCate::class, Interest::class, 'exhibitor_id', 'main_cate_id')->groupBy('main_cate_id');
    }

    public function country()
    {
        return $this->hasOne(Countries::class, 'id', 'country_id');
    }


    public function getImgAvatarAttribute()
    {
        return $this->attributes['img_avatar'] = $this->attributes['img_avatar'] != null ? Storage::disk('exhibitor')->url($this->attributes['img_avatar']) : null;
    }

    public function getOriginalImgAvatarAttribute()
    {
        return $this->attributes['img_avatar'];
    }

    public function getBannerAttribute()
    {
        return $this->attributes['banner'] = $this->attributes['banner'] != null ? Storage::disk('exhibitor')->url($this->attributes['banner']) : null;
    }

    public function getOriginalBannerAttribute()
    {
        return $this->attributes['banner'];
    }

    public function getLogoAttribute()
    {
        return $this->attributes['logo'] = $this->attributes['logo'] != null ? Storage::disk('exhibitor')->url($this->attributes['logo']) : null;
    }

    public function getOriginalLogoAttribute()
    {
        return $this->attributes['logo'];
    }

    public function appointmentOutgoing()
    {

        return $this->hasMany(SlotAppointment::class, 'owner_id', 'id');
    }

    public function appointmentIncoming()
    {

        return $this->hasMany(SlotAppointment::class, 'request_id', 'id');
    }


    public function appointmentMainCate()
    {
        return $this->belongsToMany(MainCate::class, Interest::class, 'exhibitor_id', 'main_cate_id');
    }

    public function haveSlot() {
        return $this->hasMany(SlotAvailable::class, 'owner_id', 'id')->where("owner_type", "=", "exhibitor");
    }

    public function getIncomingExhibitor()
    {

        return $this->hasMany(SlotAppointment::class, 'owner_id', 'id')->where("owner_type", "=", "exhibitor")->where("request_type", "=", "exhibitor");
    }

    public function getIncomingBuyer()
    {
        return $this->hasMany(SlotAppointment::class, 'owner_id', 'id')->where("owner_type", "=", "exhibitor")->where("request_type", "=", "buyer");
    }

    public function getOutgoingExhibitor()
    {

        return $this->hasMany(SlotAppointment::class, 'request_id', 'id')->where("request_type", "=", "exhibitor")->where("owner_type", "=", "exhibitor");
    }

    public function getOutgoingBuyer()
    {
        return $this->hasMany(SlotAppointment::class, 'request_id', 'id')->where("request_type", "=", "exhibitor")->where("owner_type", "=", "buyer");
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
