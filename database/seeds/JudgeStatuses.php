<?php

use Illuminate\Database\Seeder;

class JudgeStatuses extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$judge_statuses = [
			['id' => '5', 'title' => 'Припинено повноваження'],
			['id' => '4', 'title' => 'Відсутній на робочому місці'],
			['id' => '3', 'title' => 'У відпустці'],
			['id' => '2', 'title' => 'На лікарняному'],
			['id' => '1', 'title' => 'На роботі']
		];
	
		foreach($judge_statuses as $judge_status) {
			try {
				DB::table('judge_statuses')->insert($judge_status);
				$this->command->info('judge_status "'.$judge_status['title'].'" доданий!');
			} catch (Exception $e) {
				break ;
			}
		}
    }
}
