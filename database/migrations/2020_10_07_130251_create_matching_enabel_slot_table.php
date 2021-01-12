<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchingEnabelSlotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matching_enable_slot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exhibitor_id')->nullable();
            $table->foreign('exhibitor_id')->references('id')->on('exhibitor_list');
            $table->unsignedBigInteger('visitor_id')->nullable();
            $table->foreign('visitor_id')->references('id')->on('registers');

            $table->unsignedBigInteger('m_exhibitor_id')->nullable();
            $table->foreign('m_exhibitor_id')->references('id')->on('exhibitor_list');
            $table->unsignedBigInteger('m_visitor_id')->nullable();
            $table->foreign('m_visitor_id')->references('id')->on('registers');

            $table->unsignedBigInteger('slot_time');
            $table->foreign('slot_time')->references('id')->on('matching_slot');

            $table->unsignedBigInteger('slot_status'); // unavailiable , availiable, booked
            $table->date('slot_date');
            $table->string('booking_type',20);


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
        Schema::dropIfExists('matching_enable_slot');
    }
}
