<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJudgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('judges', function (Blueprint $table) {
            $table->smallIncrements('id');
			$table->smallInteger('court');
			$table->string('surname');
			$table->string('name');
			$table->string('patronymic');
			$table->string('photo')->nullable();
			$table->tinyInteger('status')->default(1);
            $table->timestamp('updated_status');
			$table->string('phone')->nullable();
			$table->smallInteger('rating')->default(0);
            
			$table->foreign('status')->references('id')->on('judge_statuses');
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
        Schema::dropIfExists('judges');
    }
}
