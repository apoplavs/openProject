<?php

namespace Toecyd\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;
use Toecyd\Court;
use Toecyd\EssencesCases;
use Toecyd\Judge;
use Toecyd\AutoAssignedCasesModel;

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

        foreach (Court::getCourtCodes() as $courtCode) {
            $this->saveCurlResponseToDb($courtCode, $this->getCurlResponse($courtCode));
        }
    }

    private function getCurlResponse(int $courtCode)
    {
        $curlPostFields = "sEcho=1&iColumns=6&sColumns=&iDisplayStart=0&iDisplayLength=-1&mDataProp_0=0&mDataProp_1=1&mDataProp_2=2&mDataProp_3=3&mDataProp_4=4&mDataProp_5=5&sSearch=&bRegex=false&sSearch_0=&bRegex_0=false&bSearchable_0=false&sSearch_1=&bRegex_1=false&bSearchable_1=false&sSearch_2=&bRegex_2=false&bSearchable_2=true&sSearch_3=&bRegex_3=false&bSearchable_3=true&sSearch_4=&bRegex_4=false&bSearchable_4=false&sSearch_5=&bRegex_5=false&bSearchable_5=false&q_ver=arbitr&date={$this->dateFrom}~{$this->dateTo}&sid={$courtCode}&cspec=0&sSearch=";

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

    private function saveCurlResponseToDb($courtCode, $response)
    {
        if (empty($response) || empty($response->iTotalDisplayRecords) || empty($response->aaData)) {
            return;//Дані порожні, нема чого зберігати
        }

        foreach ($response->aaData as $item) {
            $this->saveItemToDb($courtCode, $item);
        }

        echo "court {$courtCode} complete\n";
    }

    private function saveItemToDb($courtCode, $item)
    {
        $itemObj = new AutoAssignedCase($courtCode, $item);
        if (empty($itemObj->caseId)) {
            Db::table('auto_assigned_cases')->insert([
                'court'             => $itemObj->courtCode,
                'number'            => $itemObj->number,
                'date_registration' => $itemObj->dateRegistration,
                'judge'             => $itemObj->judgeId,
                'description'       => $itemObj->titleId,
                'date_composition'  => $itemObj->dateComposition,
            ]);
        }
    }
}

class AutoAssignedCase
{
    public $caseId;
    public $courtCode;
    public $number;//номер справи
    public $dateRegistration;

    public $judgeId;
    public $titleId;

    public $dateComposition;

    public function __construct(int $courtCode, array $item)
    {
        $this->courtCode = $courtCode;

        $this->number = $item[0];
        $this->dateRegistration = date('Y-m-d', strtotime($item[1]));
        $judgeNameRaw = $item[2];
        $titleRaw = $item[4];

        if (!empty($this->caseId = AutoAssignedCasesModel::getCaseId($this->courtCode, $this->dateRegistration, $this->number))) {
            return;
        }

        $judgeNameParsed = Judge::parseJudgeName($judgeNameRaw);
        $this->judgeId = Judge::getJudgeIdByParsedName($this->courtCode, $judgeNameParsed);

        $titleParsed = EssencesCases::parseTitle($titleRaw);
        $this->titleId = EssencesCases::fillIdByParsedTitle($titleParsed);

        $this->dateComposition = date('Y-m-d', strtotime($item[5]));
    }
}


