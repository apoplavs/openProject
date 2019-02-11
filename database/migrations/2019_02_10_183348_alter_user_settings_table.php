<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'ALTER TABLE `user_settings` DROP FOREIGN KEY `user_settings_user_foreign`; ALTER TABLE `user_settings` ADD CONSTRAINT `user_settings_user_foreign` FOREIGN KEY (`user`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;';
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = 'ALTER TABLE `user_settings` DROP FOREIGN KEY `user_settings_user_foreign`; ALTER TABLE `user_settings` ADD CONSTRAINT `user_settings_user_foreign` FOREIGN KEY (`user`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;';
        DB::unprepared($sql);
    }
}
