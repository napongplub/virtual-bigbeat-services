<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestIdToEnableSlot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matching_enable_slot', function (Blueprint $table) {
            //
            $table->unsignedBigInteger("owner_id")->nullable()->after('id');
            $table->string("owner_type", 50)->nullable()->after('owner_id');
            $table->unsignedBigInteger("appointment_id")->nullable()->after('slot_date');
            $table->foreign('appointment_id')->references('id')->on('matching_appointment');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matching_enable_slot', function (Blueprint $table) {
            //
        });
    }
}
