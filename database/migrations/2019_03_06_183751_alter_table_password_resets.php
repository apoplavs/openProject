<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePasswordResets extends Migration
{
    private $tbl_name = 'password_resets';
    private $fk_name = 'password_resets_user_foreign';

    public function __construct() {
        \Doctrine\DBAL\Types\Type::addType('timestamp', 'MarkTopper\DoctrineDBALTimestampType\TimestampType');
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tbl_name, function (Blueprint $table) {
            $table->timestamp('created_at')->useCurrent()->change();
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
        Schema::table($this->tbl_name, function (Blueprint $table) {
            $table->timestamp('created_at')->default(null)->change();
            $table->dropForeign(['email']);
        });
    }
}
