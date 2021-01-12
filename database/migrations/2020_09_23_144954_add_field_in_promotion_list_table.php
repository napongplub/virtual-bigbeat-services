<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldInPromotionListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotion_list', function (Blueprint $table) {
            $table->text('info')->nullable()->after('link_thumbnail');
            $table->string('description', 200)->nullable()->after('info');
            $table->boolean('active')->default(false)->after('description');
            $table->unsignedInteger('ordering')->default(0)->after('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotion_list', function (Blueprint $table) {
            //
        });
    }
}
