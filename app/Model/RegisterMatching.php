<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Model\Countries;
use App\Model\MainCate;
use App\Model\Interest;
use App\Model\SlotAvailable;
use App\Model\JobFunction;
use App\Model\NatureOfBusiness;




class RegisterMatching extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table    = "registers";

    protected $fillable = [
        "fname",
        "lname",
        "position",
        "company",
        "country",
        "website",
        "type",

    ];

    protected $hidden = [
        "email",
        "telephone",
        "mobile",
        "fax",
        "address",
        "city",
        "province",
        "postal_code",
        "nature_of_business",
        "job_level",
        "job_function",
        "role_process",
        "number_of_employees",
        "allow_matching",
        "cate_id",
        "reason_for_attending",
        "find_out_about",
        "find_out_about_other",
        "interested_to_join",
        "nature_of_business_other",
        "job_level_other",
        "job_function_other",
        "reason_for_attending_other",
        "prefix_name",
        "prefix_name_other",
        "allow_accept",
        "budget",
        "p_hash",
        "channel",
        "password",
        "remember_token",
        "p_hash"
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];

    protected $appends = ['name'];


    public function getNameAttribute()
    {
        return ucfirst($this->fname) . ' ' . ucfirst($this->lname);
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


    public function country()
    {
        return $this->hasOne(Countries::class, 'id', 'country');
    }

    public function registCountry()
    {
        return $this->hasOne(Countries::class, 'id', 'country');
    }


    public function getCreatedAtAttribute()
    {
        return date("Y-m-d H:i", strtotime($this->attributes['created_at']));
    }

    public function mainCate()
    {
        return $this->belongsToMany(MainCate::class, Interest::class, 'register_id', 'main_cate_id')->groupBy('main_cate_id');
    }

    public function appointment()
    {
        return $this->hasMany(SlotAppointment::class, 'request_id', 'id');
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
        return $this->belongsToMany(MainCate::class, Interest::class, 'register_id', 'main_cate_id');
    }

    public function haveSlot() {
        return $this->hasMany(SlotAvailable::class, 'owner_id', 'id')->where("owner_type", "=", "buyer");
    }
    public function jobFunctionRef()
    {
        return $this->hasOne(JobFunction::class, 'id', 'job_function');
    }
    public function natureOfBusinessRef()
    {
        return $this->hasOne(NatureOfBusiness::class, 'id', 'nature_of_business');
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
    
    public function getOutgoingExhibitor()
    {
        return $this->hasMany(SlotAppointment::class, 'request_id', 'id')->where("request_type", "=", "buyer")->where("owner_type", "=", "exhibitor");
    }

    public function getIncomingExhibitor()
    {
        return $this->hasMany(SlotAppointment::class, 'owner_id', 'id')->where("owner_type", "=", "buyer")->where("request_type", "=", "exhibitor");
    }
}
