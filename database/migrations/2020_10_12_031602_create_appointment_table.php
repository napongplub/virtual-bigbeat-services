<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matching_appointment', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('exhibitor_id')->nullable();
            $table->foreign('exhibitor_id')->references('id')->on('exhibitor_list');
            $table->unsignedBigInteger('visitor_id')->nullable();
            $table->foreign('visitor_id')->references('id')->on('registers');

            $table->unsignedBigInteger('m_exhibitor_id')->nullable();
            $table->foreign('m_exhibitor_id')->references('id')->on('exhibitor_list');
            $table->unsignedBigInteger('m_visitor_id')->nullable();
            $table->foreign('m_visitor_id')->references('id')->on('registers');

            $table->string('status_appointment',50); // request accept reject
            $table->string('type',20);
            $table->text("note")->nullable();
            $table->unsignedBigInteger('slot_time')->nullable();
            $table->foreign('slot_time')->references('id')->on('matching_enable_slot');


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
        Schema::dropIfExists('matching_appointment');
    }
}
