<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeZoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_zone', function (Blueprint $table) {
            
            //
            $table->id();
            $table->string("name",200);
            $table->string("utc_offset",200);
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
        Schema::table('time_zone', function (Blueprint $table) {
            //
            Schema::dropIfExists('time_zone');

        });
    }
}
