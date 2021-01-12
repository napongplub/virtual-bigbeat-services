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
use App\Model\Timezone;


use Storage;


class Exhibitor extends Authenticatable implements JWTSubject
{
    protected $table    = "exhibitor_list";

    protected $fillable = [
        'name',
        'company',
        'email',
        'mobile',
        'position',
        'category',
        'website',
        'address',
        'description',
        'logo',
        'm_name',
        'm_email',
        'm_mobile',
        'facebook',
        'youtube',
        'twitter',
        'linkedin',
        'welcome_msg',
        'offline_msg',
        'time_zone',
        'video_limit',
        'poster_limit',
        'promotion_limit',
        'brochure_limit',
        'country_id',
        'online',
        "status_email_notify_at",
        "status_email_notify",
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
        'status_email_notify_at' => 'date:Y-m-d H:i:s',
    ];

    public function category()
    {
        return $this->hasOne(Interest::class, 'id', 'category');
    }

    public function subCate()
    {
        return $this->belongsToMany(SubCate::class, Interest::class, 'exhibitor_id', 'sub_cate_id');
    }

    public function mainCate()
    {
        return $this->belongsToMany(MainCate::class, Interest::class, 'exhibitor_id', 'main_cate_id')->groupBy('main_cate_id');
    }

    public function country()
    {
        return $this->hasOne(Countries::class, 'id', 'country_id');
    }

    public function mainCateRef()
    {
        return $this->hasMany(Interest::class, "exhibitor_id", "id")
            ->join("main_category", "interest_category.main_cate_id", "=", "main_category.id");
    }

    public function videoList()
    {
        return $this->hasMany(VideoList::class);
    }

    public function eposterList()
    {
        return $this->hasMany(EposterList::class);
    }

    public function promotionList()
    {
        return $this->hasMany(PromotionList::class);
    }

    public function brochureList()
    {
        return $this->hasMany(BrochureList::class);
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

    public function appointment()
    {
        return $this->hasMany(SlotAppointment::class, 'owner_id', 'id');
    }
    public function appointmentMainCate()
    {
        return $this->belongsToMany(MainCate::class, Interest::class, 'exhibitor_id', 'main_cate_id');
    }
    public function timeZone()
    {
        return $this->hasOne(Timezone::class, 'id', 'time_zone');
    }

    public function brochureBag()
    {
        return $this->hasMany(BrochureBag::class, 'acc_id', 'id')
            ->where('brochure_bag.type', 1);
    }

    public function logVisitBooths()
    {
        return $this->hasMany(LogVisitBooths::class, 'actor_id', 'id')
            ->where('log_visit_booths.actor_type', 'exhibitor');
    }

    public function logVideo()
    {
        return $this->hasMany(LogVideo::class, 'actor_id', 'id')
            ->where('log_video.actor_type', 'exhibitor');
    }

    public function logPoster()
    {
        return $this->hasMany(LogPoster::class, 'actor_id', 'id')
            ->where('log_poster.actor_type', 'exhibitor');
    }

    public function logPromotion()
    {
        return $this->hasMany(LogPromotion::class, 'actor_id', 'id')
            ->where('log_promotion.actor_type', 'exhibitor');
    }

    public function logBrochureAccess()
    {
        return $this->hasMany(LogBrochure::class, 'actor_id', 'id')
            ->where('log_brochure.actor_type', 'exhibitor')
            ->where('log_brochure.action', 'access');
    }

    public function logBrochureDownload()
    {
        return $this->hasMany(LogBrochure::class, 'actor_id', 'id')
            ->where('log_brochure.actor_type', 'exhibitor')
            ->where('log_brochure.action', 'download');
    }

    public function logInfo()
    {
        return $this->hasMany(LogInfo::class, 'actor_id', 'id')
            ->where('log_info.actor_type', 'exhibitor');
    }

    public function logChat()
    {
        return $this->hasMany(LogChat::class, 'actor_id', 'id')
            ->where('log_chat.actor_type', 'exhibitor');
    }

    public function lastVisitBooth()
    {
        return $this->hasOne(LogVisitBooths::class, 'actor_id', 'id')
            ->select('log_visit_booths.actor_id', 'log_visit_booths.created_at')
            ->where('log_visit_booths.actor_type', 'exhibitor')
            ->latest();
    }

    public function lastLogIn()
    {
        return $this->hasOne(ExhibitorLogin::class, 'exhibitor_id', 'id')
            ->where('success', 'Y')
            ->latest();
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
