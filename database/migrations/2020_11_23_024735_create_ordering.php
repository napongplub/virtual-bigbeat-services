<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdering extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordering', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("exhibition_id")->nullable();
            $table->unsignedBigInteger("m_cate_id")->nullable();
            $table->unsignedInteger("ordering")->nullable();
            $table->foreign('exhibition_id')->references('id')->on('exhibitor_list');
            $table->foreign('m_cate_id')->references('id')->on('main_category');
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
        Schema::dropIfExists('ordering');
    }
}
