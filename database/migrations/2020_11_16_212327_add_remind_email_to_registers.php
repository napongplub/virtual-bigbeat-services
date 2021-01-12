<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemindEmailToRegisters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registers', function (Blueprint $table) {
            $table->string("status_email_remind", 20)->default('0')->nullable()->after('approve_at');
            $table->timestamp('status_email_remind_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

}
