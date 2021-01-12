<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingToAppointment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matching_appointment', function (Blueprint $table) {
            $table->unsignedBigInteger("rating_1")->nullable()->after('meeting_status');
            $table->unsignedBigInteger("rating_2")->nullable()->after('rating_1');
            $table->timestamp('rating_1_at')->nullable()->after('rating_2');
            $table->timestamp('rating_2_at')->nullable()->after('rating_1_at');


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
