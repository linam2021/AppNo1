<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('f_name')->nullable();
            $table->string('s_name')->nullable();
            $table->string('t_name')->nullable();
            $table->string('l_name')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('town')->nullable();
            $table->string('phone')->nullable();
            $table->enum('gender',['male','female'])->nullable();
            $table->string('national_id',15)->nullable();

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
        Schema::dropIfExists('users');
    }
}
