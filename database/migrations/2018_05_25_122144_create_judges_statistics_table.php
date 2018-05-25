<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJudgesStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('judges_statistics', function (Blueprint $table) {
            $table->integer('judge')->unique();
            $table->smallInteger('civil_amount')->nullable()->comment('кількість розглянутих цивільних справ');
			$table->smallInteger('civil_cases_on_time')->nullable()->comment('кількість справ розглянутих в визначені законом строки');
			$table->smallInteger('civil_average_duration')->nullable()->comment('середня тривалість розгляду справи');
			$table->smallInteger('civil_positive_judgment')->nullable()->comment('кількість рішень в яких задоволено вимоги позивача');
			$table->smallInteger('civil_negative_judgment')->nullable()->comment('кількість рішень в яких відмовлено у задоволення вимог позивача');
			$table->smallInteger('civil_other_judgment')->nullable()->comment('кількість рішень які вирішені іншим чином');
			$table->smallInteger('civil_was_appeal')->nullable()->comment('кількість рішень на які подавалась апеляція');
			$table->smallInteger('civil_approved_by_appeal')->nullable()->comment('кількість рішень які встояли в апеляції');
			$table->smallInteger('civil_was_cassation')->nullable()->comment('кількість рішень на які подавалась касація');
			$table->smallInteger('civil_approved_by_cassation')->nullable()->comment('кількість рішень які встояли в касації');
			$table->smallInteger('criminal_amount')->nullable()->comment('кількість розглянутих кримінальних справ');
			$table->smallInteger('criminal_cases_on_time')->nullable()->comment('кількість справ розглянутих в визначені законом строки');
			$table->smallInteger('criminal_average_duration')->nullable()->comment('середня тривалість розгляду справи');
			$table->smallInteger('criminal_positive_judgment')->nullable()->comment('кількість вироків в яких особу звільнено від відповідальності');
			$table->smallInteger('criminal_negative_judgment')->nullable()->comment('кількість вироків в яких особу притягнено до відповідальності');
			$table->smallInteger('criminal_was_appeal')->nullable()->comment('кількість вироків на які подавалась апеляція');
			$table->smallInteger('criminal_approved_by_appeal')->nullable()->comment('кількість вироків які встояли в апеляції');
			$table->smallInteger('criminal_was_cassation')->nullable()->comment('кількість вироків на які подавалась касація');
			$table->smallInteger('criminal_approved_by_cassation')->nullable()->comment('кількість вироків які встояли в касації');
			$table->smallInteger('adminoffence_amount')->nullable()->comment('кількість розглянутих справ в порядку КУпАП');
			$table->smallInteger('adminoffence_cases_on_time')->nullable()->comment('кількість справ розглянутих в визначені законом строки');
			$table->smallInteger('adminoffence_average_duration')->nullable()->comment('середня тривалість розгляду справи');
			$table->smallInteger('adminoffence_positive_judgment')->nullable()->comment('кількість постанов в яких особу звільнено від відповідальності');
			$table->smallInteger('adminoffence_negative_judgment')->nullable()->comment('кількість постанов в яких особу притягнено до відповідальності');
			$table->smallInteger('adminoffence_was_appeal')->nullable()->comment('кількість постанов на які подавалась апеляція');
			$table->smallInteger('adminoffence_approved_by_appeal')->nullable()->comment('кількість постанов які встояли в апеляції');
			$table->smallInteger('admin_amount')->nullable()->comment('кількість розглянутих адміністративних справ');
			$table->smallInteger('commercial_amount')->nullable()->comment('кількість розглянутих господарських справ');
	
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
        Schema::dropIfExists('judge_statistics');
    }
}
