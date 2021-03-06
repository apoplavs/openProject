<?php

use Illuminate\Database\Seeder;

class Usertypes extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$usertypes = [
			['id' => '5', 'title' => 'Адміністратор'],
			['id' => '4', 'title' => 'Видалений користувач'],
			['id' => '3', 'title' => 'PRO аккаунт'],
			['id' => '2', 'title' => 'Користувач з підтвердженим email'],
			['id' => '1', 'title' => 'Зареєстрований користувач, НЕ підтверджений email']
		];
		
		foreach($usertypes as $usertype) {
			try {
				DB::table('usertypes')->insert($usertype);
				$this->command->info('usertype "'.$usertype['title'].'" доданий!');
			} catch (Exception $e) {
				break ;
			}
		}
	}
}
