<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchingReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matching_report', function (Blueprint $table) {
            $table->id();
            $table->string("request_name", 250)->nullable();
            $table->string("request_company", 250)->nullable();
            $table->string("request_type", 20)->nullable();
            $table->string("owner_name", 250)->nullable();
            $table->string("owner_company", 250)->nullable();
            $table->string("owner_type", 20)->nullable();
            $table->string("type", 50)->nullable();
            $table->string("status_request", 50)->nullable();
            $table->string("status_cancel", 50)->nullable();
            $table->string("cancel_by", 250)->nullable();
            $table->string("status_meeting", 50)->nullable();
            $table->unsignedBigInteger("meeting_request_join")->nullable();
            $table->unsignedBigInteger("meeting_owner_join")->nullable();
            $table->string("start_date", 50)->nullable();
            $table->string("start_time", 50)->nullable();
            $table->string("end_time", 50)->nullable();
            $table->string("meeting_room", 50)->nullable();
            $table->string("meeting_status", 50)->nullable();
            $table->unsignedBigInteger("request_rating")->nullable();
            $table->unsignedBigInteger("owner_rating")->nullable();
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
        Schema::dropIfExists('matching_report');
    }
}
