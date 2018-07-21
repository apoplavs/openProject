<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AlterCourtsTable
 */
class AlterCourtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // якщо існує таблиця courts, і не існує поле head_judge
		if (Schema::hasTable('courts') && !Schema::hasColumn('courts', 'head_judge')) {
			Schema::table('courts', function (Blueprint $table) {
				$table->integer('head_judge')->nullable()->after('jurisdiction')->comment('суддя який є головою суду');
				
				$table->foreign('head_judge')->references('id')->on('judges');
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
		Schema::table('courts', function (Blueprint $table) {
			$table->dropColumn('head_judge');
		});
    }
}
