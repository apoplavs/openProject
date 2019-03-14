<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePasswordResets extends Migration
{
    private $tbl_name = 'password_resets';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("ALTER TABLE {$this->tbl_name} CHANGE created_at created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");

        Schema::table($this->tbl_name, function (Blueprint $table) {
            $table->foreign('email')->references('email')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("ALTER TABLE {$this->tbl_name} CHANGE created_at created_at TIMESTAMP NULL DEFAULT NULL");

        Schema::table($this->tbl_name, function (Blueprint $table) {
            $table->dropForeign(['email']);
        });


    }
}
