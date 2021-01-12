<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class VideoList extends Model
{
    //
    protected $table = "video_list";
    
    protected $fillable = [
        "id",
        "exhibitor_id",
        "link",
        "link_thumbnail",
        "type",
        "info",
        "description",
        "active",
        "order"
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' => 'date:Y-m-d H:i:s',
    ];

}
