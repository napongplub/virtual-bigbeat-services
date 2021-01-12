<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\MainCate;
use App\Model\SubCate;

class InterestCategoryByExhibitorId extends Model
{
    // fontend request

    // id: number;
    // exhibitor_id: string;
    // main_catetory_id: string;
    // main_category_th: string;
    // main_category_en: string;
    // sub_category_id: string;
    // sub_category: string;
    // sub_category_th: string;
    // sub_category_en: string;
    // created_at: string;
    // updated_at: string;

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
