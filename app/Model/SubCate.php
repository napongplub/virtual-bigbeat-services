<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\MainCate;

class SubCate extends Model
{
    //
    protected $table = "sub_category";
    
    protected $fillable = [
        "name",
        "man_cate"
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' => 'date:Y-m-d H:i:s',
    ];

    public function main_catgery()
    {
        return $this->hasOne(MainCate::class, 'id', 'main_cate');
    }
}
