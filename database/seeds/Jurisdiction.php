<?php

use Illuminate\Database\Seeder;

/**
 * Class Jurisdiction
 */
class Jurisdiction extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$jurisdictions = [
			['id' => '4', 'title' => 'Інша'],
			['id' => '3', 'title' => 'Господарська'],
			['id' => '2', 'title' => 'Адміністративна'],
			['id' => '1', 'title' => 'Загальна']
		];
	
		foreach($jurisdictions as $jurisdiction) {
			try {
				DB::table('jurisdictions')->insert($jurisdiction);
				$this->command->info($jurisdiction['title'].' юрисдикція додана!');
			} catch (Exception $e) {
				break ;
			}
		}
    }
}
