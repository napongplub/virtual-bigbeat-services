<?php

namespace App\Model;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    use Timestamp;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'time_zone';

}
