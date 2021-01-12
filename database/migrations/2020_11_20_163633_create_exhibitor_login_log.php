<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExhibitorLoginLog extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('exhibitor_login_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("exhibitor_id")->nullable();
            $table->foreign('exhibitor_id')->references('id')->on('exhibitor_list')->onDelete('cascade');
            $table->string("ip");
            $table->text("user_agent");
            $table->enum('success', ['Y', 'N'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('exhibitor_login_log');
    }
}
