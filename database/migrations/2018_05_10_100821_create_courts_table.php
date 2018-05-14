<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courts', function (Blueprint $table) {
			$table->smallInteger('court_code');
			$table->string('name');
			$table->tinyInteger('instance_code');
			$table->tinyInteger('region_code');
			$table->string('address')->nullable();
			$table->string('phone')->nullable();
			$table->string('email')->nullable();
			$table->string('site')->nullable();
	
			$table->primary('court_code');
			$table->foreign('instance_code')->references('instance_code')->on('instances');
			$table->foreign('region_code')->references('region_code')->on('regions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courts');
    }
}
