<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterJudgesCivilStatisticTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('judges_civil_statistic') && !Schema::hasColumn('judges_civil_statistic', 'cases_not_on_time')) {
            Schema::table('judges_civil_statistic', function (Blueprint $table) {
                $table->smallInteger('cases_not_on_time')->default(0)->after('cases_on_time')->comment('кількість розглянутих справ з порушенням строків');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('judges_civil_statistic', function (Blueprint $table) {
            $table->dropColumn('cases_not_on_time');
        });
    }
}
