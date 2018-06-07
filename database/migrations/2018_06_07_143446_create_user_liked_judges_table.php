<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLikedJudgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_liked_judges', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user');
			$table->integer('judge');
			$table->boolean('like')->defalt(false);
			$table->boolean('unlike')->defalt(false);
	
			$table->foreign('user')->references('id')->on('users');
			$table->foreign('judge')->references('id')->on('judges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_liked_judges');
    }
}
