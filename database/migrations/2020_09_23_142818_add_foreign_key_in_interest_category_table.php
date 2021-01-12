<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyInInterestCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interest_category', function (Blueprint $table) {
            //
            $table->foreign('exhibitor_id')->references('id')->on('exhibitor_list');
            $table->foreign('register_id')->references('id')->on('registers');
            $table->foreign('main_cate_id')->references('id')->on('main_category');
            $table->foreign('sub_cate_id')->references('id')->on('sub_category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interest_category', function (Blueprint $table) {
            //
        });
    }
}
