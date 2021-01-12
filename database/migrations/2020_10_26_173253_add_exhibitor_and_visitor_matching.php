<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExhibitorAndVisitorMatching extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matching_appointment', function (Blueprint $table) {

            // $table->unsignedBigInteger('m_exhibitor_id')->nullable();
            // $table->foreign('m_exhibitor_id')->references('id')->on('exhibitor_list');
            // $table->unsignedBigInteger('m_visitor_id')->nullable();
            // $table->foreign('m_visitor_id')->references('id')->on('registers');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matching_appointment', function (Blueprint $table) {
            //
        });
    }
}
