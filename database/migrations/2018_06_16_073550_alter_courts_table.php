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
		if (Schema::hasTable('courts') && !Schema::hasColumn('courts', 'jurisdiction')) {
			Schema::table('courts', function (Blueprint $table) {
				$table->tinyInteger('jurisdiction')->default(1)->after('region_code');
				
				$table->foreign('jurisdiction')->references('id')->on('jurisdictions');
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
			$table->dropColumn('jurisdiction');
		});
    }
}
