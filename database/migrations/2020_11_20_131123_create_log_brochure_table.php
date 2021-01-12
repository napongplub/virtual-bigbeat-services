<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogBrochureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_brochure', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ref_id');
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('actor_id');
            $table->string('actor_type', 10);
            $table->string('action', 150);
            $table->string('data', 255);
            $table->string('description', 255);
            $table->string('user_agent', 255);
            $table->string('client_timezone', 200);
            $table->timestamps();
            $table->foreign('owner_id')->references('id')->on('exhibitor_list');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_brochure');
    }
}
