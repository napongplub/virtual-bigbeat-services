<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReigsterForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registers', function (Blueprint $table) {
            $table->foreign('cate_id')->references('id')->on('main_category');
            $table->foreign('country')->references('id')->on('countries');
            $table->foreign('nature_of_business')->references('id')->on('nature_of_business');
            $table->foreign('job_level')->references('id')->on('job_level');
            $table->foreign('job_function')->references('id')->on('job_function');
            $table->foreign('role_process')->references('id')->on('role_process');
            $table->foreign('number_of_employees')->references('id')->on('number_of_employees');
        });
    }
}
