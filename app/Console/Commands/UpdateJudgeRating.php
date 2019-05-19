<?php

namespace Toecyd\Console\Commands;

use Illuminate\Console\Command;
use Toecyd\Judge;
use Toecyd\JudgesStatistic;

class UpdateJudgeRating extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:judge_rating';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Оновити/порахувати рейтинг кожного судді і записати в БД';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $judges_with_statistic = Judge::getJudgesWithStatistic();

        foreach ($judges_with_statistic as $key => $judge) {
            // отимуємо статистику по типах справ
            $adminoffence_statistic = JudgesStatistic::getAdminoffenceStatistic($judge->id);
            $civil_statistic =  JudgesStatistic::getCivilStatistic($judge->id);
            $criminal_statistic = JudgesStatistic::getCriminalStatistic($judge->id);

            // вибираємо в масив статистику, яка існує
			$arr_statistic = $this->selectStatistic($adminoffence_statistic, $criminal_statistic, $civil_statistic);
			
			// якщо статистика не пуста - визначаємо медіану
			if (!empty($arr_statistic)) {
				$judges_with_statistic[$key]->rating = $this->median($arr_statistic);
				$judges_with_statistic[$key]->save();
				echo "for judge {$judge->id} rating is ready".PHP_EOL;
			}
        }
        
    }


    /**
     * набір масивву з значень яка != 0
     * @return array
     */
    private function selectStatistic($adminoffence_statistic, $criminal_statistic, $civil_statistic) {
        // якщо статистики немає
        if (!$adminoffence_statistic && !$criminal_statistic && !$civil_statistic) {
            return [];
        }
        $common_statistic = [];
        if (array_key_exists('approved_by_appeal', $civil_statistic)) {
			$common_statistic[] = $civil_statistic['approved_by_appeal'];
        }
        if (array_key_exists('approved_by_appeal', $criminal_statistic)) {
			$common_statistic[] = $criminal_statistic['approved_by_appeal'];
        }
        if (array_key_exists('approved_by_appeal', $adminoffence_statistic)) {
			$common_statistic[] =  $adminoffence_statistic['approved_by_appeal'];
        }

        if (array_key_exists('cases_on_time', $civil_statistic)) {
			$common_statistic[] = $civil_statistic['cases_on_time'];
        }
        if (array_key_exists('cases_on_time', $criminal_statistic)) {
			$common_statistic[] = $criminal_statistic['cases_on_time'];
        }
        if (array_key_exists('cases_on_time', $adminoffence_statistic)) {
			$common_statistic[] = $adminoffence_statistic['cases_on_time'];
        }

        return $common_statistic;
    }
	
	
	/**
	 * @param $arr
	 * @return float|int
	 */
	function median ($arr) { //Медиана от массива $arr
		sort ($arr);
		$count = count($arr);
		$middle = floor($count/2);
		if ($count%2) {
			return intval($arr[$middle]);
		}
		else {
			return intval(($arr[$middle-1]+$arr[$middle])/2);
		}
	}
}
