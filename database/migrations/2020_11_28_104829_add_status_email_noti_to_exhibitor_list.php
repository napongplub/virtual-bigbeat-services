<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusEmailNotiToExhibitorList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exhibitor_list', function (Blueprint $table) {
            $table->string("status_email_notify", 20)->default('0')->nullable()->after('updated_at');
            $table->timestamp('status_email_notify_at')->nullable()->after('status_email_notify');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

}
