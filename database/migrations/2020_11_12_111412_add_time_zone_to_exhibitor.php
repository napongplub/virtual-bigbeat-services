<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeZoneToExhibitor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exhibitor_list', function (Blueprint $table) {
            
            //
            $table->unsignedBigInteger('time_zone')->nullable()->after('country_id');
            $table->foreign('time_zone')->references('id')->on('time_zone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exhibitor_list', function (Blueprint $table) {
            //
        });
    }
}
