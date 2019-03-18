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

            // рахуємо загальну статистику
            $common_statistic = $this->countCommonStatistic($adminoffence_statistic, $criminal_statistic, $civil_statistic);

            $judges_with_statistic[$key]->rating = intval(($common_statistic['competence'] + $common_statistic['timeliness']) / 2);
            $judges_with_statistic[$key]->save();
            echo "for judge {$judge->id} rating is ready".PHP_EOL;
        }
        
    }


    /**
     * розразунок загальної статистики
     * @return array
     */
    private function countCommonStatistic($adminoffence_statistic, $criminal_statistic, $civil_statistic) {
        // якщо статистики немає
        if (!$adminoffence_statistic && !$criminal_statistic && !$civil_statistic) {
            return NULL;
        }
        $common_statistic = [];
        $all_approved = 0;
        $count_judgements = 0;
        if (array_key_exists('approved_by_appeal', $civil_statistic)) {
            $all_approved += $civil_statistic['approved_by_appeal'];
            $count_judgements++;
        }
        if (array_key_exists('approved_by_appeal', $criminal_statistic)) {
            $all_approved += $criminal_statistic['approved_by_appeal'];
            $count_judgements++;
        }
        if (array_key_exists('approved_by_appeal', $adminoffence_statistic)) {
            $all_approved += $adminoffence_statistic['approved_by_appeal'];
            $count_judgements++;
        }
          if ($count_judgements != 0) {
               $common_statistic['competence'] = intval($all_approved / $count_judgements);
          } else {
               $common_statistic['competence'] = 0;
          }


        $all_approved = 0;
        $count_judgements = 0;
        if (array_key_exists('cases_on_time', $civil_statistic)) {
            $all_approved += $civil_statistic['cases_on_time'];
            $count_judgements++;
        }
        if (array_key_exists('cases_on_time', $criminal_statistic)) {
            $all_approved += $criminal_statistic['cases_on_time'];
            $count_judgements++;
        }
        if (array_key_exists('cases_on_time', $adminoffence_statistic)) {
            $all_approved += $adminoffence_statistic['cases_on_time'];
            $count_judgements++;
        }
          if ($count_judgements != 0) {
               $common_statistic['timeliness'] = intval($all_approved / $count_judgements);
          } else {
               $common_statistic['timeliness'] = 0;
          }


        return $common_statistic;
    }
}
