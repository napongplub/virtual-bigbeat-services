<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMainIdInSubcateTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_category', function (Blueprint $table) {

            // $table->unsignedBigInteger("main_cate")->nullable()->after("name");
            // $table->foreign('main_cate')->references('id')->on('main_category');

            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    // public function down()
    // {
    //     Schema::table('sub_category', function (Blueprint $table) {
    //         //
    //     });
    // }
}
