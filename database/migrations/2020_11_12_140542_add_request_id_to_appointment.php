<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestIdToAppointment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matching_appointment', function (Blueprint $table) {

            $table->unsignedBigInteger("request_id")->nullable()->after('id');
            $table->unsignedBigInteger("owner_id")->nullable()->after('request_id');
            $table->string("request_type", 50)->nullable()->after('owner_id');
            $table->string("owner_type", 50)->nullable()->after('request_type');
            $table->text("reject_note")->nullable()->after('note');

            //
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
