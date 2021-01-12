<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Storage;

class BrochureList extends Model
{
    //
    protected $table = "brochure_list";

    protected $fillable = [
        "id",
        "exhibitor_id",
        "link",
        "link_thumbnail",
        "info",
        "description",
        "active",
        "order",
        "type",
        "file_type",
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' => 'date:Y-m-d H:i:s',
    ];

    public function getLinkAttribute()
    {
        // return $this->attributes['link'] = $this->attributes['link'] != null ? Storage::disk('exhibitor')->url($this->attributes['link']) : null;
        if ($this->attributes['type'] != 'link') {
            return $this->attributes['link'] = $this->attributes['link'] != null ? Storage::disk('exhibitor')->url($this->attributes['link']) : null;
        }
        else{
            return $this->attributes['link'];
        }
    }

    public function getLinkThumbnailAttribute()
    {
        return $this->attributes['link_thumbnail'] = $this->attributes['link_thumbnail'] != null ? Storage::disk('exhibitor')->url($this->attributes['link_thumbnail']) : null;
    }

    public function getOriginalLinkAttribute()
    {
        return $this->attributes['link'];
    }

    public function getOriginalLinkThumbnailAttribute()
    {
        return $this->attributes['link_thumbnail'];
    }
}
