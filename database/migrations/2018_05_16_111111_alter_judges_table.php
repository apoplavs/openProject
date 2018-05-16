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
		if (Schema::hasTable('judges') && !Schema::hasColumn('judges', 'due_date_status')) {
			Schema::table('judges', function (Blueprint $table) {
				$table->date('due_date_status')->nullable()->after('updated_status');
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
			$table->dropColumn('due_date_status');
		});
    }
}
