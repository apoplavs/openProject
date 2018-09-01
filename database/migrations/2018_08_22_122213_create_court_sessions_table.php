<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourtSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('court_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('court')->comment('id суду, в якому розглядається справа');
            $table->dateTime('date')->nullable()->comment('дата судового засідання');
            $table->unsignedSmallInteger('judge1')->comment('id судді 1');
            $table->unsignedSmallInteger('judge2')->nullable()->comment('id судді 2, якщо справа розглядається колегіально');
            $table->unsignedSmallInteger('judge3')->nullable()->comment('id судді 3, якщо справа розглядається колегіально');
            $table->tinyInteger('forma')->comment('форма судочинства');
            $table->string('number')->nullable()->comment('номер справи');
            $table->text('involved')->nullable()->comment('сторони по справі');
            $table->string('description')->nullable()->comment('суть справи');
            $table->text('add_address')->nullable()->comment('адреса суду');

            $table->foreign('court')->references('court_code')->on('courts');
            $table->foreign('judge1')->references('id')->on('judges');
            $table->foreign('judge2')->references('id')->on('judges');
            $table->foreign('judge3')->references('id')->on('judges');
            $table->foreign('forma')->references('justice_kind')->on('justice_kinds');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('court_sessions');
    }
}
