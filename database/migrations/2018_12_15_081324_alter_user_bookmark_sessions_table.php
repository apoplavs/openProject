<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserBookmarkSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (Schema::hasTable('user_bookmark_sessions') && !Schema::hasColumn('user_bookmark_sessions', 'note')) {
			Schema::table('user_bookmark_sessions', function (Blueprint $table) {
				$table->string('note')->nullable()->comment('примітка до даної закладки');
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
		Schema::table('user_bookmark_sessions', function (Blueprint $table) {
			$table->dropColumn('note');
		});
    }
}
