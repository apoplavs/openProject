<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBookmarkCourtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bookmark_courts', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user');
			$table->smallInteger('court');
	
			$table->foreign('user')->references('id')->on('users');
			$table->foreign('court')->references('court_code')->on('courts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_bookmark_courts');
    }
}
