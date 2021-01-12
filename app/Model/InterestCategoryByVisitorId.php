<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\MainCate;
use App\Model\SubCate;

class InterestCategoryByVisitorId extends Model
{
 
    protected $table = "interest_category";


    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' => 'date:Y-m-d H:i:s',
    ];

    public function main_cate()
    {
        return $this->hasOne(MainCate::class, 'id', 'main_cate_id');
    }

    public function sub_cate()
    {
        return $this->belongsToMany(SubCate::class,'id','sub_cate_id');
    }
}
