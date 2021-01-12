<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\MainCate;
use App\Model\SubCate;

class Interest extends Model
{
    protected $table = "interest_category";
    
    protected $fillable = [
        "type",
        "exhibitor_id",
        "register_id",
        "main_cate_id",
        "sub_cate_id"
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];

    public function interest_cate_name() {
        return $this->hasManyThrough(
            MainCate::class, 
            SubCate::class,
            'id',
            'id',
            'main_cate_id',
            'sub_cate_id'
        );
    }

    // public function main_cate()
    // {
    //     return $this->hasOne(MainCate::class, 'id', 'main_cate_id');
    // }

    // public function sub_cate()
    // {
    //     return $this->hasOne(SubCate::class,'id','sub_cate_id');
    // }
    
}
