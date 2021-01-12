<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlotStatusTypeToEnableSlot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matching_enable_slot', function (Blueprint $table) {
            $table->string("slot_status_type", 50)->nullable()->after('slot_status');

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
