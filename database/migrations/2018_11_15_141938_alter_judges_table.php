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
		if (Schema::hasTable('judges') && !Schema::hasColumn('judges', 'address')) {
			Schema::table('judges', function (Blueprint $table) {
				$table->string('address')->nullable()->comment('адреса суду в якому працює даний суддя');
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
			$table->dropColumn('address');
		});
    }
}
