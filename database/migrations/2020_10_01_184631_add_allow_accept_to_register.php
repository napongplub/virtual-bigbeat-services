<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowAcceptToRegister extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registers', function (Blueprint $table) {

            $table->enum("allow_accept", ["Y", "N"])->default("N")->after("interested_to_join");
            $table->unsignedBigInteger("prefix_name")->nullable()->after("id");
            $table->string("prefix_name_other", 150)->nullable()->after("prefix_name");
            $table->string("find_out_about_other", 255)->nullable()->after("find_out_about");
            $table->foreign('prefix_name')->references('id')->on('prefix_name');


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
        Schema::table('registers', function (Blueprint $table) {
            //
        });
    }
}
