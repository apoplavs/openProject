<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMysqlTriggerUserSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TRIGGER insert_user_settings AFTER INSERT ON `users`
                FOR EACH ROW
                    BEGIN
                        INSERT INTO `user_settings`(`user`) 
                        VALUES (NEW.`id`);
                    END;';

        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = 'DROP TRIGGER if exists insert_user_settings;';

        DB::unprepared($sql);
    }
}
