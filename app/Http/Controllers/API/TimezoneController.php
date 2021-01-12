<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\Timezone;

class TimezoneController extends Controller
{
    /**
     * The user repository instance.
     */
    protected $timezone;

    /**
     * Create a new controller instance.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct(Timezone $timezone)
    {
        $this->timezone = $timezone;
    }

    function index() {
        return Timezone::all()->toArray();
    }

    function get($id) {
        return Timezone::find($id);
    }
}
