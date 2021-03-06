<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJudgesCriminalStatisticTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('judges_criminal_statistic', function (Blueprint $table) {
            $table->integer('judge')->unique();
            $table->smallInteger('amount')->default(0)->comment('кількість розглянутих кримінальних справ');
            $table->smallInteger('cases_on_time')->default(0)->comment('кількість справ розглянутих в визначені законом строки');
            $table->smallInteger('average_duration')->default(0)->comment('середня тривалість розгляду однієї справи');
            $table->smallInteger('positive_judgment')->default(0)->comment('кількість вироків в яких особу звільнено від відповідальності');
            $table->smallInteger('negative_judgment')->default(0)->comment('кількість вироків в яких особу притягнено до відповідальності');
            $table->smallInteger('was_appeal')->default(0)->comment('кількість вироків на які подавалась апеляція');
            $table->smallInteger('approved_by_appeal')->default(0)->comment('кількість вироків які встояли в апеляції');
            $table->smallInteger('not_approved_by_appeal')->default(0)->comment('кількість вироків які не встояли в апеляції');
            $table->smallInteger('was_cassation')->default(0)->comment('кількість вироків на які подавалась касація');
            $table->smallInteger('approved_by_cassation')->default(0)->comment('кількість вироків які встояли в касації');
            $table->smallInteger('not_approved_by_cassation')->default(0)->comment('кількість вироків які не встояли в касації');

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
        Schema::dropIfExists('judges_criminal_statistic');
    }
}
