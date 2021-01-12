<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Storage;

class BrochureBag extends Model {
    //
    protected $table = "brochure_bag";
    public $timestamps = false;

    protected $fillable = [
        "id",
        "acc_id",
        "type",
        "brochure_id",
        "created_at"
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',

    ];


}
