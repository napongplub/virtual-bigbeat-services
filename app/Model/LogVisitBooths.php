<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Exhibitor;
use App\Model\Register;

class LogVisitBooths extends Model
{
    protected $table = 'log_visit_booths';

    protected $fillable = [
        "ref_id",
        "owner_id",
        "actor_id",
        "actor_type",
        "action",
        "data",
        "description"
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' =>  'date:Y-m-d H:i:s',
    ];

    public function Exhibitor(){
        return $this->hasMany(Exhibitor::class, "id", 'actor_id')->with(["mainCate","country"]);
    }

    public function Visitor(){
        return $this->hasMany(Register::class, "id", 'actor_id')->with(["mainCate","country"]);
    }
}
