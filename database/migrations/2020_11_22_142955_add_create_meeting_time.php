<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreateMeetingTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matching_appointment', function (Blueprint $table) {
            $table->timestamp('create_room_at')->nullable()->after('meeting_status');
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
            $table->dropColumn('create_room_at');
        });
    }
}
