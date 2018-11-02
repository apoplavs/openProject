<?php

namespace Toecyd\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Toecyd\Court;
use Toecyd\lib\JudgeNameParsed;
use DateTime;

/**
 * Class CourtSessions
 * @package Toecyd\Console\Commands
 *
 * Отримує судові засідання з Державного реєстру судових рішень та записує їх в БД
 */
class CourtSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:court_sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отримати список судових засідань';

    /* @var array */
    private $time_statistics = [];

    /* @var array */
    private $existing_sessions = [];

    /* @var array */
    private $existing_judges = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }
	
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 * @throws \Exception
	 */
    public function handle() {
        foreach (Court::getCourtCodes() as $court_code) {
            $this->time_statistics['start'] = microtime(true);
            $response = $this->getCurlResponse($court_code);
            $this->time_statistics['after_curl'] = microtime(true);

            $curl_time = number_format($this->time_statistics['after_curl'] - $this->time_statistics['start'], 3);

            echo "Court {$court_code}: Curl time: {$curl_time} seconds. ";

            if (!empty($response)) {
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
	 * Формує запит до державного реєстру судових справ, відправляє цей запит за допомогою cURL та отримує результат
	 *
	 * @param int $court_code
	 *
	 * @return array
	 * @throws \Exception
	 */
    private function getCurlResponse(int $court_code) : array {
        $curl_post_fields = "q_court_id={$court_code}";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://gl.ki.court.gov.ua/new.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_post_fields);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, '0');//відмовляюсь від верифікації https

        $headers = [
            'Pragma: no-cache',
            'Origin: https://gl.ki.court.gov.ua',
            'Accept-Encoding: gzip, deflate, br',
            'Host: gl.ki.court.gov.ua',
            'Accept-Language: uk,en;q=0.9,en-US;q=0.8,ru;q=0.7',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Accept: application/json, text/javascript, /; q=0.01',
            'Cache-Control: no-cache',
            'X-Requested-With: XMLHttpRequest',
            'Connection: keep-alive',
            'Referer: https://gl.ki.court.gov.ua/sud2601/gromadyanam/csz/',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }
        curl_close($ch);

        return json_decode($result);
    }

    /**
     * Зберігає масив судових засідань у базу даних
     *
     * @param int    $court_code
     * @param array $response
     *
     * @return void
     */
    private function saveCurlResponse(int $court_code, array $response) {
        $this->existing_sessions = $this->getExistingSessions($court_code);
        $this->existing_judges = $this->getExistingJudges($court_code);

        $sessions_to_insert = [];

        foreach ($response as $item) {
            $session_to_insert = $this->getSessionsToInsert($court_code, $item);
            if (!empty($session_to_insert)) {
                $sessions_to_insert[] = $session_to_insert;
            }
        }

//        var_dump($sessions_to_insert);
        Db::table('court_sessions')->insert($sessions_to_insert);

        echo "Total sessions: "
            . (!empty($response) ? count($response) : 0) . ". Inserted sessions: "
            . (!empty($sessions_to_insert) ? count($sessions_to_insert) : 0) . ". ";
    }

    /**
     * Отримує з БД масив уже записаних судових засідань, щоб не вставляти в базу дублі
     *
     * @param int $court_code
     *
     * @return array
     */
    private function getExistingSessions(int $court_code) {
//        Постановка задачі говорить, що в запит нижче було б непогано вставити WHERE date BETWEEN (місяць плюс-мінус тиждень)
//        Але у response зустрічаються справи з date = 2019-11-07 і тому подібними датами
//        Todo: обдумати цей момент.
        $raw_result = DB::table('court_sessions')
            ->select('id', 'date', 'number')
            ->where('court', '=', $court_code)
            ->get()
            ->toArray();

        $result = [];
        foreach ($raw_result as $item) {
            $result[$item->number][$item->date] = $item->id;
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
     * @param int       $court_code
     * @param \stdClass $item
     *
     * @return array
     */
    private function getSessionsToInsert(int $court_code, \stdClass $item) {
        $item->date = date('Y-m-d H:i:s', strtotime($item->date));
        if (isset($this->existing_sessions[$item->number][$item->date])) {
            return [];
        }

        try {
            $judge_ids = $this->getJudgeIds($court_code, $item->judge);
            $forma_id = $this->getFormaId($item->forma);
        } catch (\Exception $e) {
            echo "Судове засідання не вдалося записати в базу ({$e->getMessage()})\n";
            return [];
        }

        $result = [
            'court'       => $court_code,
            'date'        => $item->date,
            'forma'       => $forma_id,
            'number'      => $item->number,
            'involved'    => $item->involved,
            'description' => mb_substr($item->description, 0, 255),
            'add_address' => $item->add_address,
        ];

        return array_merge($result, $judge_ids);
    }

    /**
     * Приймає рядок виду 'Іванов І.І., Петров П.П., Орлов О.О.'
     * Повертає масив виду [judge1 => 100, judge2 => 200, judge3 => 300], де 100, 200, 300 -- id відповідних судей у БД
     *
     * @param int    $court_code
     * @param string $judge_names_string
     *
     * @return array
     * @throws \Exception
     */
    private function getJudgeIds(int $court_code, string $judge_names_string) {
        $judge_names_arr = explode(',', $judge_names_string);
        $judge_names_arr = array_map('trim', $judge_names_arr);

        $judge_names_count = count($judge_names_arr);
        if (!in_array($judge_names_count, [1, 2, 3])) {
            throw new \Exception("Кількість суддів: {$judge_names_count}. Треба, щоб було 1 або 3");
        }

        $result = ['judge2' => null, 'judge3' => null];
        foreach ($judge_names_arr as $i => $judge_name_raw) {
            $result['judge' . ($i + 1)] = $this->getJudgeId($court_code, $judge_name_raw);
        }
        return $result;
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
     * @param string $forma
     *
     * @return int
     * @throws \Exception
     */
    private function getFormaId(string $forma): int {
        $forma_to_id = [
            'Цивільні справи'                => 1,
            'Кримінальні справи'             => 2,
            'Господарські справи'            => 3,
            'Адміністративні справи'         => 4,
            'Справи про адмінправопорушення' => 5,
        ];

        $result = $forma_to_id[trim($forma)] ?? null;
        if (is_null($result)) {
            throw new \Exception("Не вдалося розпарсити форму судочинства: '{$forma}'");
        }
        return $result;
    }
}