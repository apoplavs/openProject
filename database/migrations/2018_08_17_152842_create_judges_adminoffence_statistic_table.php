<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJudgesAdminoffenceStatisticTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('judges_adminoffence_statistic', function (Blueprint $table) {
            $table->integer('judge')->unique();
            $table->smallInteger('amount')->default(0)->comment('кількість розглянутих справ в порядку КУпАП');
            $table->smallInteger('cases_on_time')->default(0)->comment('кількість справ розглянутих в визначені законом строки');
            $table->smallInteger('average_duration')->default(0)->comment('середня тривалість розгляду справи');
            $table->smallInteger('positive_judgment')->default(0)->comment('кількість постанов в яких особу звільнено від відповідальності');
            $table->smallInteger('negative_judgment')->default(0)->comment('кількість постанов в яких особу притягнено до відповідальності');
            $table->smallInteger('was_appeal')->default(0)->comment('кількість постанов на які подавалась апеляція');
            $table->smallInteger('approved_by_appeal')->default(0)->comment('кількість постанов які встояли в апеляції');
            $table->smallInteger('not_approved_by_appeal')->default(0)->comment('кількість постанов які не встояли в апеляції');

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
        Schema::dropIfExists('judges_adminoffence_statistic');
    }
}
