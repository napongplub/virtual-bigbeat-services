<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLimitMediaToExhibitorList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exhibitor_list', function (Blueprint $table) {
            $table->integer('video_limit')->default('1')->after('logo');
            $table->integer('poster_limit')->default('1')->after('video_limit');
            $table->integer('promotion_limit')->default('1')->after('poster_limit');
            $table->integer('brochure_limit')->default('10')->after('promotion_limit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

}
