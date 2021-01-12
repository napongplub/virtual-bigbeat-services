<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableExhibitorListAddOnlineColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exhibitor_list', function (Blueprint $table) {
            $table->boolean('online')->default(true)->after('offline_msg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exhibitor_list', function (Blueprint $table) {
            $table->dropColumn('online');
        });
    }
}
