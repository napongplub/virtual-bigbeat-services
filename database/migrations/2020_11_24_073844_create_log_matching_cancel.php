<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogMatchingCancel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_cancel_matching', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("request_id")->nullable();
            $table->string("role", 50)->nullable();
            $table->string("type", 50)->nullable();
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
        Schema::dropIfExists('log_cancel_matching');
    }
}
