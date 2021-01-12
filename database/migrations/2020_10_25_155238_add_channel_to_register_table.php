<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChannelToRegisterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registers', function (Blueprint $table) {
            $table->string("channel",100)->nullable()->after("id");
            $table->string("position",150)->nullable()->after("company");

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registers', function (Blueprint $table) {
            //
        });
    }
}
