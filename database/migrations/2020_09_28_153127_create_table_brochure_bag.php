<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBrochureBag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brochure_bag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("visitor_id")->nullable();
            $table->unsignedBigInteger("exhibitor_id")->nullable();
            $table->unsignedBigInteger("brochure_id")->nullable();
            $table->timestamp('created_at')->nullable();
            $table->foreign('visitor_id')->references('id')->on('registers');
            $table->foreign('exhibitor_id')->references('id')->on('exhibitor_list');
            $table->foreign('brochure_id')->references('id')->on('brochure_list');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brochure_bag');
    }
}