<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('name');
			$table->string('surname')->nullable();
			$table->string('phone')->nullable();
			$table->string('town')->nullable();
			$table->string('region')->nullable();
            $table->string('email')->unique();
            $table->string('password');
			$table->string('photo')->nullable();
			$table->tinyInteger('usertype')->default(1);
            $table->rememberToken();
            $table->timestamps();
	
			$table->foreign('usertype')->references('id')->on('usertypes');
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
