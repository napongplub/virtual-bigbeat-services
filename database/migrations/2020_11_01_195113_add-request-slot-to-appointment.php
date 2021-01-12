<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestSlotToAppointment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matching_appointment', function (Blueprint $table) {
            $table->unsignedBigInteger('request_slot')->nullable()->after('note');
            $table->foreign('request_slot')->references('id')->on('matching_enable_slot');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matching_appointment', function (Blueprint $table) {
            //
        });
    }
}
