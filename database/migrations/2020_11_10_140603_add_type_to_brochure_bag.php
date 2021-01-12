<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToBrochureBag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brochure_bag', function (Blueprint $table) {
            //
            $table->bigInteger('acc_id')->after('id');
            $table->string('type', 25)->after('acc_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

}
