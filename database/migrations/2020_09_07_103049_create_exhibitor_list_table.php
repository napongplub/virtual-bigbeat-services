<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExhibitorListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exhibitor_list', function (Blueprint $table) {
            $table->id();
            $table->string("name", 150)->nullable();
            $table->string("company", 150)->nullable();
            $table->string("mobile", 30)->nullable();
            $table->string("position", 50)->nullable();
            $table->string("email", 80)->nullable();
            $table->string("website", 250)->nullable();
            $table->text("address")->nullable();
            $table->text("description")->nullable();
            $table->string("logo", 150)->nullable();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exhibitor_list');
    }
}
