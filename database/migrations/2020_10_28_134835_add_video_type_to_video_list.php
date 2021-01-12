<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoTypeToVideoList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_list', function (Blueprint $table) {
            //
            $table->enum('type', ["youtube", "facebook"])->default("youtube")->after('link_thumbnail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_list', function (Blueprint $table) {
            //
        });
    }
}
