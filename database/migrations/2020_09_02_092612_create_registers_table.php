<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('registers', function (Blueprint $table) {
            $table->id();
            $table->string("fname", 100)->nullable();
            $table->string("lname", 100)->nullable();
            $table->string("company", 150)->nullable();
            $table->string("address", 500)->nullable();
            $table->string("city", 100)->nullable();
            $table->string("province", 80)->nullable();
            $table->string("postal_code", 20)->nullable();
            $table->unsignedBigInteger("country")->nullable();
            $table->string("telephone",50)->nullable();
            $table->string("mobile",50)->nullable();
            $table->string("fax",50)->nullable();
            $table->string("email", 80)->nullable();
            $table->string("website", 150)->nullable();
            $table->unsignedBigInteger("nature_of_business")->nullable();
            $table->string("nature_of_business_other",150)->nullable();
            $table->unsignedBigInteger("job_level")->nullable();
            $table->string("job_level_other",150)->nullable();
            $table->unsignedBigInteger("job_function")->nullable();
            $table->string("job_function_other",150)->nullable();
            $table->unsignedBigInteger("role_process")->nullable();
            $table->unsignedBigInteger("number_of_employees")->nullable();
            $table->enum("allow_matching", ["Y", "N"])->default("N");
            $table->unsignedBigInteger('cate_id')->nullable();
            $table->text("reason_for_attending")->nullable();
            $table->string("reason_for_attending_other",150)->nullable();
            $table->text("find_out_about")->nullable();
            $table->enum("interested_to_join", ["Y", "N"])->default("N");
            $table->string("password");
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('registers');
    }
}
