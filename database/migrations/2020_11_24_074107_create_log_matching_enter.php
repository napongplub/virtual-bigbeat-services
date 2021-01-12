<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogMatchingEnter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_join_matching', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("appointment_id")->nullable();
            $table->unsignedBigInteger("join_id")->nullable();
            $table->string("role", 50)->nullable();
            $table->string("meeting_id", 50)->nullable();
            $table->string("meeting_code", 50)->nullable();
            $table->string("meeting_status", 50)->nullable();
            $table->text("appointment_data")->nullable();
            $table->text("slot_data")->nullable();
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
        Schema::dropIfExists('log_join_matching');
    }
}
