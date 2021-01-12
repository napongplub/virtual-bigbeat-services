<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvatarBannerToExhibitorList extends Migration
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
            $table->string("img_avatar", 250)->nullable()->after('logo');
            $table->string('banner', 250)->nullable()->after('img_avatar');
            $table->text('welcome_msg')->nullable()->after('description');
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
