<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorLoginLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_login_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("visitor_id")->nullable();
            $table->foreign('visitor_id')->references('id')->on('registers')->onDelete('cascade');
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
    public function down()
    {
        Schema::dropIfExists('visitor_login_log');
    }
}
