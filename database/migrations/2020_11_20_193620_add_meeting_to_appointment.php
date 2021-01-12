<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMeetingToAppointment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matching_appointment', function (Blueprint $table) {
            $table->string("meeting_id", 50)->nullable()->after('slot_time');
            $table->string("meeting_status", 20)->nullable()->after('meeting_id');
            $table->unsignedBigInteger("rank")->nullable()->after('meeting_status');

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
            $table->dropColumn("meeting_id");
            $table->dropColumn("meeting_status");
        });
    }
}
