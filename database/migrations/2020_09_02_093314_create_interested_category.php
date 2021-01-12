<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterestedCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interest_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type')->default('0');
            $table->unsignedBigInteger("exhibitor_id")->nullable();
            // $table->foreign('exhibitor_id')->references('id')->on('exhibitor_list');
            $table->unsignedBigInteger("register_id")->nullable();
            // $table->foreign('register_id')->references('id')->on('registers');
            $table->unsignedBigInteger("main_cate_id")->nullable();
            // $table->foreign('main_cate_id')->references('id')->on('main_category');
            $table->unsignedBigInteger("sub_cate_id")->nullable();
            // $table->foreign('sub_cate_id')->references('id')->on('sub_category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interest_category');
    }
}
