<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exhibitor_id');
            $table->foreign('exhibitor_id')->references('id')->on('exhibitor_list');
            $table->text("link")->nullable();
            $table->text("link_thumbnail")->nullable();
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
        Schema::dropIfExists('video_list');
    }
}
