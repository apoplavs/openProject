<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJudgesCommercialStatisticTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('judges_commercial_statistic', function (Blueprint $table) {
            $table->integer('judge')->unique();
            $table->smallInteger('amount')->nullable()->comment('кількість розглянутих господарських справ');
            $table->smallInteger('cases_on_time')->nullable()->comment('кількість справ розглянутих в визначені законом строки');
            $table->smallInteger('average_duration')->nullable()->comment('середня тривалість розгляду однієї справи');
            $table->smallInteger('positive_judgment')->nullable()->comment('кількість рішень в яких задоволено вимоги позивача');
            $table->smallInteger('negative_judgment')->nullable()->comment('кількість рішень в яких відмовлено у задоволенні вимог позивача');
            $table->smallInteger('other_judgment')->nullable()->comment('кількість рішень які вирішені іншим чином');
            $table->smallInteger('was_appeal')->nullable()->comment('кількість рішень на які подавалась апеляція');
            $table->smallInteger('approved_by_appeal')->nullable()->comment('кількість рішень які встояли в апеляції');
            $table->smallInteger('not_approved_by_appeal')->nullable()->comment('кількість рішень які не встояли в апеляції');
            $table->smallInteger('was_cassation')->nullable()->comment('кількість рішень на які подавалась касація');
            $table->smallInteger('approved_by_cassation')->nullable()->comment('кількість рішень які встояли в касації');
            $table->smallInteger('not_approved_by_cassation')->nullable()->comment('кількість рішень які не встояли в касації');

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
        Schema::dropIfExists('judges_commercial_statistic');
    }
}
