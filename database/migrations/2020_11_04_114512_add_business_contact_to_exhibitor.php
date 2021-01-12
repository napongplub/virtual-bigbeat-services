<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessContactToExhibitor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exhibitor_list', function (Blueprint $table) {

            $table->string("m_name", 250)->nullable()->after('banner');
            $table->string("m_email", 250)->nullable()->after('m_name');
            $table->string("m_mobile", 250)->nullable()->after('m_email');

            $table->string("facebook", 250)->nullable()->after('website');
            $table->string("youtube", 250)->nullable()->after('facebook');
            $table->string("twitter", 250)->nullable()->after('youtube');
            $table->string("linkedin", 250)->nullable()->after('twitter');

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
        Schema::table('exhibitor_list', function (Blueprint $table) {
            //
        });
    }
}
