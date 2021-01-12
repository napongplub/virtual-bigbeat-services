<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentUnreadChatToRegisters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registers', function (Blueprint $table) {
            $table->string("current_unread_chat", 10)->nullable()->after("id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

}
