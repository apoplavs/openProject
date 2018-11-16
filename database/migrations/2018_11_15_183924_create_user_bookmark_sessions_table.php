<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBookmarkSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bookmark_sessions', function (Blueprint $table) {
            $table->increments('id');
			$table->unsignedInteger('user');
			$table->unsignedInteger('court_session');
	
			$table->foreign('user')->references('id')->on('users');
			$table->foreign('court_session')->references('id')->on('court_sessions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_bookmark_sessions');
    }
}
