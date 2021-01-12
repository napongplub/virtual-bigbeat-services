<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Model\Countries;
use App\Model\JobFunction;
use App\Model\RoleProcess;
use App\Model\InterestCategoryByVisitorId;
use App\Model\MainCate;
use App\Model\Interest;
use App\Model\Timezone;
use App\Model\NatureOfBusiness;
use Storage;





class Register extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table    = "registers";

    protected $fillable = [
        "fname",
        "lname",
        "position",
        "company",
        "address",
        "city",
        "province",
        "postal_code",
        "country",
        "telephone",
        "mobile",
        "fax",
        "email",
        "website",
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
        "type",
        "approve_at",
        "time_zone",
        "status_email_remind",
        "status_email_remind_at",
        "status_email_notify_at",
        "status_email_notify",
        "profile_image"

    ];

    protected $hidden = [
        'password', 'remember_token', 'p_hash'
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
        'approve_at' =>  'date:Y-m-d H:i:s',
        'status_email_remind_at' =>  'date:Y-m-d H:i:s',
        'status_email_notify_at' => 'date:Y-m-d H:i:s',
    ];

    protected $appends = ['full_name'];


    public function getFullNameAttribute()
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

    public function job_function()
    {
        return $this->hasOne(JobFunction::class, 'id', 'job_function');
    }

    public function role_process()
    {
        return $this->hasOne(RoleProcess::class, 'id', 'role_process');
    }

    public function getCreatedAtAttribute()
    {
        return date("Y-m-d H:i", strtotime($this->attributes['created_at']));
    }

    public function category()
    {
        return $this->hasMany(InterestCategoryByVisitorId::class, 'register_id', 'id');
    }

    public function numberOfEmployeesRef()
    {
        return $this->hasOne(NumberOfEmployees::class, 'id', 'number_of_employees');
    }

    public function jobFunctionRef()
    {
        return $this->hasOne(JobFunction::class, 'id', 'job_function');
    }


    public function natureOfBusinessRef()
    {
        return $this->hasOne(NatureOfBusiness::class, 'id', 'nature_of_business');
    }

    public function jobLevelRef()
    {
        return $this->hasOne(JobLevel::class, 'id', 'job_level');
    }

    public function countryRef()
    {
        return $this->hasOne(Countries::class, 'id', 'country');
    }

    public function role()
    {
        return $this->hasOne(RoleProcess::class, 'id', 'role_process');
    }

    public function prefix()
    {
        return $this->hasOne(PrefixName::class, 'id', 'prefix_name');
    }

    public function mainCate()
    {
        return $this->belongsToMany(MainCate::class, Interest::class, 'register_id', 'main_cate_id')->groupBy('main_cate_id');
    }

    public function appointment()
    {
        return $this->hasMany(SlotAppointment::class, 'owner_id', 'id');
    }
    public function appointmentMainCate()
    {
        return $this->belongsToMany(MainCate::class, Interest::class, 'register_id', 'main_cate_id');
    }
    public function timeZone()
    {
        return $this->hasOne(Timezone::class, 'id', 'time_zone');
    }

    public function brochureBag()
    {
        return $this->hasMany(BrochureBag::class, 'acc_id', 'id')
            ->where('brochure_bag.type', 2);
    }

    public function mainCateRef()
    {
        return $this->hasMany(Interest::class, "register_id", "id")
            ->join("main_category", "interest_category.main_cate_id", "=", "main_category.id");
    }

    public function logVisitBooths()
    {
        return $this->hasMany(LogVisitBooths::class, 'actor_id', 'id')
            ->where('log_visit_booths.actor_type', 'visitor');
    }

    public function logVideo()
    {
        return $this->hasMany(LogVideo::class, 'actor_id', 'id')
            ->where('log_video.actor_type', 'visitor');
    }

    public function logPoster()
    {
        return $this->hasMany(LogPoster::class, 'actor_id', 'id')
            ->where('log_poster.actor_type', 'visitor');
    }

    public function logPromotion()
    {
        return $this->hasMany(LogPromotion::class, 'actor_id', 'id')
            ->where('log_promotion.actor_type', 'visitor');
    }

    public function logBrochureAccess()
    {
        return $this->hasMany(LogBrochure::class, 'actor_id', 'id')
            ->where('log_brochure.actor_type', 'visitor')
            ->where('action', 'access');
    }

    public function logBrochureDownload()
    {
        return $this->hasMany(LogBrochure::class, 'actor_id', 'id')
            ->where('log_brochure.actor_type', 'visitor')
            ->where('action', 'download');
    }

    public function logInfo()
    {
        return $this->hasMany(LogInfo::class, 'actor_id', 'id')
            ->where('log_info.actor_type', 'visitor');
    }

    public function logChat()
    {
        return $this->hasMany(LogChat::class, 'actor_id', 'id')
            ->where('log_chat.actor_type', 'visitor');
    }

    public function lastVisitBooth()
    {
        return $this->hasOne(LogVisitBooths::class, 'actor_id', 'id')
            ->select('log_visit_booths.actor_id', 'log_visit_booths.created_at')
            ->where('log_visit_booths.actor_type', 'visitor')
            ->latest();
    }

    public function lastLogIn()
    {
        return $this->hasOne(VisitorLogin::class, 'visitor_id', 'id')
            ->where('success', 'Y')
            ->latest();
    }

    public function prefixName () {
        return $this->hasOne(PrefixName::class, "id", "prefix_name")
        ->select("id", "name_en");
    }

    public function budget () {
        return $this->hasOne(Budget::class, "id", "budget")
        ->select("id", "name_en");
    }

    public function getProfileImageAttribute()
    {
        return $this->attributes['profile_image'] = $this->attributes['profile_image'] != null ? Storage::disk('register')->url($this->attributes['profile_image']) : null;
    }

    public function getOriginalProfileImageAttribute()
    {
        return $this->attributes['profile_image'];
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
