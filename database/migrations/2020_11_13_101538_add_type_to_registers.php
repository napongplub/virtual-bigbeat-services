<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToRegisters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registers', function (Blueprint $table) {
            //
            $table->bigInteger("type");
            $table->timestamp('approve_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

}
