<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutoAssignedCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_assigned_cases', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('court')->comment('id суду, в який надійшла справа');
            $table->string('number')->nullable()->comment('номер справи');
            $table->date('date_registration')->nullable()->comment('дата реєстрації');
            $table->unsignedSmallInteger('judge')->comment('id головуючого судді');
            $table->date('date_composition')->nullable()->comment('дата визначення складу суду');
            

            $table->foreign('court')->references('court_code')->on('courts');
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
        Schema::dropIfExists('auto_assigned_cases');
    }
}
