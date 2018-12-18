<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUserSettingsTable
 */
class CreateUserSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_settings', function (Blueprint $table) {
			$table->unsignedInteger('user');
			$table->tinyInteger('email_notification_1')->default(1)->comment('якщо в судді, якого користувач відстежує змінився статус');
			$table->tinyInteger('email_notification_2')->default(1)->comment('якщо по справі яку користувач відстежує додалось нове судове засідання');
			$table->tinyInteger('email_notification_3')->default(1)->comment('якщо по справі яку користувач відстежує в будь-якого судді змінився статус');
			$table->tinyInteger('email_notification_4')->default(1)->comment('повідомлення за 1 день до судового засідання яке користувач відстежує');
			$table->tinyInteger('email_notification_5')->default(1)->comment('пропозиції судової практики для користувача');
			$table->tinyInteger('email_notification_6')->default(1)->comment('новини, пропозиції, реклама');
	
			$table->foreign('user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_settings');
    }
}
