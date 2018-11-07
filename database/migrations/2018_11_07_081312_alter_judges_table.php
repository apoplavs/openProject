<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterJudgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // якщо існує таблиця judges, і не існує поле previous_work
		if (Schema::hasTable('judges') && !Schema::hasColumn('judges', 'previous_work')) {
			Schema::table('judges', function (Blueprint $table) {
				$table->unsignedSmallInteger('previous_work')->nullable()->comment('попереднє місце роботи');
				
				$table->foreign('previous_work')->references('id')->on('judges');
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
        Schema::table('judges', function (Blueprint $table) {
			$table->dropColumn('previous_work');
		});
    }
}
