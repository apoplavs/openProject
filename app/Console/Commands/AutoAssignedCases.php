<?php

namespace Toecyd\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

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

    private $dateFrom;
    private $dateTo;

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
        $this->dateFrom = $this->argument('date');
        $this->dateTo = date('d.m.Y', strtotime('+1 month', strtotime($this->dateFrom)));

        foreach ($this->getCourtIds() as $courtId) {
            $this->saveCurlResponseToDb($courtId, $this->getCurlResponse($courtId));
        }
    }

    private function getCourtIds()
    {
        $rows = DB::table('courts')
            ->select('court_code')
            ->whereNotIn('region_code', [1, 5, 12]) //відкидаємо АР Крим, Донецьку, Луганську області
            ->where('court_code', '<', 2800) // відкидаємо спеціалізовані суди
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $result[] = $row->court_code;
        }
        return $result;
    }

    private function getCurlResponse(int $courtId)
    {
        $curlPostFields = "sEcho=1&iColumns=6&sColumns=&iDisplayStart=0&iDisplayLength=-1&mDataProp_0=0&mDataProp_1=1&mDataProp_2=2&mDataProp_3=3&mDataProp_4=4&mDataProp_5=5&sSearch=&bRegex=false&sSearch_0=&bRegex_0=false&bSearchable_0=false&sSearch_1=&bRegex_1=false&bSearchable_1=false&sSearch_2=&bRegex_2=false&bSearchable_2=true&sSearch_3=&bRegex_3=false&bSearchable_3=true&sSearch_4=&bRegex_4=false&bSearchable_4=false&sSearch_5=&bRegex_5=false&bSearchable_5=false&q_ver=arbitr&date={$this->dateFrom}~{$this->dateTo}&sid={$courtId}&cspec=0&sSearch=";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://ds.ki.court.gov.ua/post_test2.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPostFields);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, '0');//відмовляюсь від верифікації https

        $headers = array();
        $headers[] = "Pragma: no-cache";
        $headers[] = "Origin: https://ds.ki.court.gov.ua";
        $headers[] = "Accept-Encoding: gzip, deflate, br";
        $headers[] = "Host: ds.ki.court.gov.ua";
        $headers[] = "Accept-Language: uk,en;q=0.9,en-US;q=0.8,ru;q=0.7";
        $headers[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8";
        $headers[] = "Accept: application/json, text/javascript, /; q=0.01";
        $headers[] = "Cache-Control: no-cache";
        $headers[] = "X-Requested-With: XMLHttpRequest";
        $headers[] = "Cookie: PHPSESSID=3ft43d3…MG2Z6570";
        $headers[] = "Connection: keep-alive";
        $headers[] = "Referer: https://ds.ki.court.gov.ua/sud2603/gromadyanam/list_auto_cases/";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close ($ch);

        $result = json_decode($result);

        return $result;
    }

    private function saveCurlResponseToDb($courtId, $response)
    {
        if (empty($response) || empty($response->iTotalDisplayRecords) || empty($response->aaData)) {
            return;//Дані порожні, нема чого зберігати
        }

        foreach ($response->aaData as $item) {
            $this->saveItemToDb($courtId, $item);
        }

        echo "court {$courtId} complete\n";
    }

    private function saveItemToDb($courtId, $item)
    {
        $itemObj = new AutoAssignedCase($courtId, $item);
        Db::table('auto_assigned_cases')->insert([
            'court' => $itemObj->courtId,
            'number' => $itemObj->number,
            'date_registration' => $itemObj->dateRegistration,
            'judge' => $itemObj->judgeId,
            'description' => $itemObj->descriptionId,
            'date_composition' => $itemObj->dateComposition,
        ]);
    }
}

class AutoAssignedCase
{
    public $caseId;
    public $courtId;
    public $number;//номер справи
    public $dateRegistration;
    public $judgeNameRaw;

    /* @var JudgeNameParsed */
    public $judgeNameParsed;
    public $judgeId;

    public $descriptionRaw;
    public $descriptionParsed;
    public $descriptionId;

    public $dateComposition;

    public function __construct(int $courtId, array $item)
    {
        $this->courtId = $courtId;

        $this->number = $item[0];
        $this->dateRegistration = date('Y-m-d', strtotime($item[1]));
        $this->judgeNameRaw = $item[2];
        $this->descriptionRaw = $item[4];

        $this->fillCaseId();
        if (!empty($this->caseId->items)) {
            return;
        }

        $this->parseJudgeName();
        $this->fillJudgeId();

        $this->parseDescription();
        $this->fillDescriptionId();

        $this->dateComposition = date('Y-m-d', strtotime($item[5]));
    }

    private function fillCaseId()
    {
        $rows = DB::table('auto_assigned_cases')
            ->select('id')
            ->where('court', '=', $this->courtId)
            ->where('date_registration', '=', $this->dateRegistration)
            ->where('number', '=', $this->number)
            ->get();

        foreach ($rows as $row) {
            // записали в caseId значення id з першого рядка
            $this->caseId = $row->id;
            break;
        }
    }

    private function parseJudgeName()
    {
        $judgeNameRaw = $this->judgeNameRaw;

        $matches = [];
        if (preg_match("/головуючий суддя:\s{0,1}(.+);\s{0,1}суддя-доповідач/iu", $judgeNameRaw, $matches))
        {
            $judgeNameRaw = $matches[1];
        }

        $matches = [];
        if (preg_match("/^(\w*) (\w{1})\.\s{0,1}(\w{1})\.$/Uui", $judgeNameRaw, $matches)) {
            // Варіант "Шевченко А.Б."
            $this->judgeNameParsed = new JudgeNameParsed($matches[1], $matches[2], $matches[3]);
        } elseif (preg_match("/^(\w*) (\w*) (\w*)$/Uui", $judgeNameRaw, $matches)) {
            // Варіант "Шевченко Анатолій Борисович"
            $this->judgeNameParsed = new JudgeNameParsed($matches[1], mb_substr($matches[2], 0, 1), mb_substr($matches[3], 0, 1));
        } else {
            throw new Exception("Не вдалось розпарсити ім'я судді: '{$judgeNameRaw}'");
        }
    }

    private function fillJudgeId()
    {
        $rows = Db::table('judges')
            ->select('id')
            ->where('court', '=', $this->courtId)
            ->where('surname', 'LIKE', $this->judgeNameParsed->surname)
            ->where('name', 'LIKE', $this->judgeNameParsed->name . '%')
            ->where('patronymic', 'LIKE', $this->judgeNameParsed->patronymic . '%')
            ->get();

        foreach ($rows as $row) {
            // записали в judgeId значення id з першого рядка
            $this->judgeId = $row->id;
            break;
        }

        if (empty($this->judgeId)) {
            $this->judgeId = Db::table('judges')->insertGetId([
                'court' => $this->courtId,
                'surname' => $this->judgeNameParsed->surname,
                'name' => $this->judgeNameParsed->name,
                'patronymic' => $this->judgeNameParsed->patronymic,
            ]);
        }
    }

    private function parseDescription()
    {
        $description = $this->descriptionRaw;
        if (mb_strlen($description) < 5) {
            return;
        }

        $description = mb_strtolower($description);

        $matches = [];
        if (preg_match("/.+ (про .+)/ui", $description, $matches)) {
            $description = $matches[1];
        }

        if (mb_strlen($description) >= 255) {
            return;
        }

        if (preg_match("/ \w. \?\w./u", $description) || preg_match("/\d\d+/u", $description)) {
            return;
        }

        // Видаляємо небуквенні символи з кінця рядка, потрібно щоб рядок закінчувався на "\w$"
        $description = preg_replace("/\W{0,}$/u", '', $description);

        $this->descriptionParsed = $description;
    }

    private function fillDescriptionId()
    {
        $rows = Db::table('essences_cases')
            ->select('id')
            ->where('title', '=', $this->descriptionParsed)
            ->get();

        foreach ($rows as $row) {
            // записали в descriptionId значення id з першого рядка
            $this->descriptionId = $row->id;
            break;
        }

        if (empty($this->descriptionId)) {
            $this->descriptionId = Db::table('essences_cases')->insertGetId([
                'title' => $this->descriptionParsed,
            ]);
        }
    }
}

class JudgeNameParsed
{
    public $surname;
    public $name;
    public $patronymic;

    public function __construct($surname, $name, $patronymic)
    {
        $this->surname = $surname;

        if (mb_strlen($name) != 1) {
            throw new Exception("Ініціал має складатися з однієї букви, проте маємо " . var_export($name, 1));
        }
        $this->name = $name;

        if (mb_strlen($patronymic) != 1) {
            throw new Exception("Ініціал має складатися з однієї букви, проте маємо " . var_export($patronymic, 1));
        }
        $this->patronymic = $patronymic;
    }
}
