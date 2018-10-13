<?php

namespace Toecyd\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Toecyd\Court;
use Toecyd\JudgeNameParsed;
use DateTime;

/**
 * Class AutoAssignedCases
 * @package Toecyd\Console\Commands
 *
 * Отримує автопризначені судові справи з Державного реєстру судових рішень та записує їх в БД
 */
class AutoAssignedCases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:auto_assigned_cases {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отримати список автопризначених справ, що надійшли до суду, від вказаної дати + 1 міс';

    /* @var DateTime */
    private $date_from;

    /* @var DateTime */
    private $date_to;

    /* @var string */
    private $date_format = 'd.m.Y';

    /* @var array */
    private $time_statistics = [];

    /* @var array */
    private $existing_cases = [];

    /* @var array */
    private $existing_judges = [];

    /**
     * Завантажує дані по автопризначеним справам з державного реєстру судових справ та зберігає їх в БД
     *
     * @return void
     */
    public function handle() {
        $this->initDateParams();

        foreach (Court::getCourtCodes() as $court_code) {
            $this->time_statistics['start'] = microtime(true);
            $response = $this->getCurlResponse($court_code);
            $this->time_statistics['after_curl'] = microtime(true);

            $curl_time = number_format($this->time_statistics['after_curl'] - $this->time_statistics['start'], 3);

            echo "Court {$court_code}: Curl time: {$curl_time} seconds. ";

            if (!empty($response) && !empty($response->iTotalDisplayRecords) && !empty($response->aaData)) {
                $this->saveCurlResponse($court_code, $response);
            } else {
                echo "Curl response is empty. ";
            }

            $this->time_statistics['after_all'] = microtime(true);
            $total_time = number_format($this->time_statistics['after_all'] - $this->time_statistics['start'], 3);
            echo "Total time: {$total_time} seconds\n";
        }
    }

    /**
     * Ініціалізує параметри $this->date_from та $this->date_to.
     * Перевіряє $this->argument('date') на коректність
     */
    private function initDateParams() {
        $this->date_from = DateTime::createFromFormat($this->date_format, $this->argument('date'));
        if (empty($this->date_from)) {
            throw new \Exception(
                "Параметр date='{$this->argument('date')}' не відповідає формату '{$this->date_format}'"
            );
        }

        $date_min = DateTime::createFromFormat('Y-m-d', '2014-07-21');
        $date_max = (new DateTime())->modify('-1 month');

        if ($this->date_from < $date_min || $this->date_from > $date_max) {
            throw new \Exception(
                "Параметр date='{$this->argument('date')}' вийшов за межі діапазона "
                ."'{$date_min->format($this->date_format)} - {$date_max->format($this->date_format)}'"
            );
        }

        $this->date_to = clone $this->date_from;
        $this->date_to->modify('+1 month');
    }

    /**
     * Формує запит до державного реєстру судових справ, відправляє цей запит за допомогою cURL та отримує результат
     *
     * @param int $court_code
     *
     * @return object
     */
    private function getCurlResponse(int $court_code) {
        $curl_post_fields = "sEcho=1&iColumns=6&sColumns=&iDisplayStart=0&iDisplayLength=-1&mDataProp_0=0&mDataProp_1=1&mDataProp_2=2&mDataProp_3=3&mDataProp_4=4&mDataProp_5=5&sSearch=&bRegex=false&sSearch_0=&bRegex_0=false&bSearchable_0=false&sSearch_1=&bRegex_1=false&bSearchable_1=false&sSearch_2=&bRegex_2=false&bSearchable_2=true&sSearch_3=&bRegex_3=false&bSearchable_3=true&sSearch_4=&bRegex_4=false&bSearchable_4=false&sSearch_5=&bRegex_5=false&bSearchable_5=false&q_ver=arbitr&date={$this->date_from->format($this->date_format)}~{$this->date_to->format($this->date_format)}&sid={$court_code}&cspec=0&sSearch=";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://ds.ki.court.gov.ua/post_test2.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_post_fields);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, '0');//відмовляюсь від верифікації https

        $headers = [
            'Pragma: no-cache',
            'Origin: https://ds.ki.court.gov.ua',
            'Accept-Encoding: gzip, deflate, br',
            'Host: ds.ki.court.gov.ua',
            'Accept-Language: uk,en;q=0.9,en-US;q=0.8,ru;q=0.7',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Accept: application/json, text/javascript, /; q=0.01',
            'Cache-Control: no-cache',
            'X-Requested-With: XMLHttpRequest',
            'Connection: keep-alive',
            'Referer: https://ds.ki.court.gov.ua/sud2603/gromadyanam/list_auto_cases/',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }
        curl_close($ch);

        return json_decode($result);;
    }

    /**
     * Зберігає масив судових справ у базу даних
     *
     * @param int    $court_code
     * @param object $response
     *
     * @return void
     */
    private function saveCurlResponse($court_code, $response) {
        $this->existing_cases = $this->getExistingCases($court_code, $response->aaData);
        $this->existing_judges = $this->getExistingJudges($court_code);

        $cases_to_insert = [];

        foreach ($response->aaData as $item) {
            $case_to_insert = $this->getCaseToInsert($court_code, $item);
            if (!empty($case_to_insert)) {
                $cases_to_insert[] = $case_to_insert;
            }
        }

        Db::table('auto_assigned_cases')->insert($cases_to_insert);

        echo "Total cases: "
            . (!empty($response->aaData) ? count($response->aaData) : 0) . ". Inserted cases: "
            . (!empty($cases_to_insert) ? count($cases_to_insert) : 0) . ". ";
    }

    /**
     * Отримує з БД уже існуючі автопризначені справи
     *
     * @param int   $court_code
     * @param array $response_data - масив, з якого беруться номери судових справ
     *
     * @return array
     */
    private function getExistingCases($court_code, $response_data) {
        $cases_data = DB::table('auto_assigned_cases')
            ->select('id', 'number', 'date_registration')
            ->where('court', '=', $court_code)
            ->whereIn('number', array_column($response_data, 0))
            ->get()
            ->toArray();

        $result = [];

        foreach ($cases_data as $cases_item) {
            $result[$cases_item->number][$cases_item->date_registration] = $cases_item->id;
        }

        return $result;
    }

    /**
     * Отримує з БД дані по вже існуючим суддям
     *
     * @param int $court_code
     *
     * @return array
     */
    private function getExistingJudges($court_code) {
        return DB::table('judges')
            ->select('id', 'name', 'surname', 'patronymic')
            ->where('court', '=', $court_code)
            ->get()
            ->toArray();
    }

    /**
     * Отримує id судді в БД, користуючись заздалегідь вибраними даними по існуючим суддям.
     * Якщо суддя із вказаним ПІБ в БД відсутній -- записує ПІБ судді в БД
     *
     * @param int    $court_code
     * @param string $judge_name_raw
     *
     * @return int
     */
    private function getJudgeId($court_code, $judge_name_raw) {
        $judge_id = 0;
        $parsed = JudgeNameParsed::parseJudgeName($judge_name_raw);
        foreach ($this->existing_judges as $key => $row) {
            if ($row->surname == $parsed->surname) {
                if (($row->name == $parsed->name || mb_substr($row->name, 0, 1) == $parsed->name)
                    && ($row->patronymic == $parsed->patronymic || mb_substr($row->patronymic, 0, 1) == $parsed->patronymic)) {
                    $judge_id = $row->id;
                } elseif (mb_substr($parsed->name, 0, 1) == $row->name && mb_strlen($parsed->name) > 1
                    && mb_substr($parsed->patronymic, 0, 1) == $row->patronymic && mb_strlen($parsed->patronymic) > 1) {
                    // Випадок, коли у базі лежать лише ініціали судді, а прийшло повне ім'я.
                    // Запам'ятовуємо judge_id, а також оновлюємо інфу в базі і в масивi $existing_judges
                    $judge_id = $row->id;
                    DB::table('judges')
                        ->where('id', $judge_id)
                        ->update(['name' => $parsed->name, 'patronymic' => $parsed->patronymic]);
                    $this->existing_judges[$key]->name = $parsed->name;
                    $this->existing_judges[$key]->patronymic = $parsed->patronymic;
                }
            }
        }
        if (empty($judge_id)) {
            $inserted_data = array_merge(['court' => $court_code], (array)$parsed);
            $judge_id = DB::table('judges')->insertGetId($inserted_data);
            $this->existing_judges[] = (object)array_merge(['id' => $judge_id], $inserted_data);
        }
        return $judge_id;
    }

    /**
     * Приводить дані по автопризначеній справі у такий вигляд, щоб їх можна було записати в БД.
     * Форматує дати, знаходить id судді за його прізвищем, і тд.
     *
     * @param int   $court_code
     * @param array $item
     *
     * @return array
     */
    private function getCaseToInsert($court_code, $item) {
        $item_assoc = [
            'number'            => $item[0],
            'date_registration' => date('Y-m-d', strtotime($item[1])),
            'judge_name_raw'    => $item[2],
            'date_composition'  => date('Y-m-d', strtotime($item[5])),
        ];

        if (isset($this->existing_cases[$item_assoc['number']][$item_assoc['date_registration']])) {
            return [];
        }

        try {
            $judge_id = $this->getJudgeId($court_code, $item_assoc['judge_name_raw']);
        } catch (\Exception $e) {
            echo "Справу не вдалося записати в базу ({$e->getMessage()})\n";
            return [];
        }

        return [
            'court'             => $court_code,
            'number'            => $item_assoc['number'],
            'date_registration' => $item_assoc['date_registration'],
            'judge'             => $judge_id,
            'date_composition'  => $item_assoc['date_composition'],
        ];
    }
}