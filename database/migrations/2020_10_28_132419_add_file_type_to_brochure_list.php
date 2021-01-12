<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileTypeToBrochureList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brochure_list', function (Blueprint $table) {
            $table->enum("type", ["image", "pdf"])->after("description")->default("image");
            $table->string("file_type", 30)->after("type")->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brochure_list', function (Blueprint $table) {
            //
        });
    }
}
