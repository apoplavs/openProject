<?php

namespace Toecyd\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;
use Toecyd\Court;
use Toecyd\CourtSession;
use Toecyd\EssencesCase;
use Toecyd\Judge;

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
    protected $description = 'Отримати список всіх судових засідань всіх судів';

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
        foreach (Court::getCourtCodes() as $courtCode) {
            $this->saveCurlResponseToDb($courtCode, $this->getCurlResponse($courtCode));
        }
    }

    public function saveCurlResponseToDb(int $courtCode, $response)
    {
        if (empty($response)) {
            return;//Дані порожні, нема чого зберігати
        }

        foreach ($response as $item) {
            $this->saveItemToDb($courtCode, $item);
        }

        echo "court {$courtCode} complete\n";
    }

    public function getCurlResponse(int $courtCode)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://gl.ki.court.gov.ua/new.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "q_court_id={$courtCode}");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, '0');//відмовляюсь від верифікації https

        $headers = array();
        $headers[] = "Pragma: no-cache";
        $headers[] = "Origin: https://gl.ki.court.gov.ua";
        $headers[] = "Accept-Encoding: gzip, deflate, br";
        $headers[] = "Host: gl.ki.court.gov.ua";
        $headers[] = "Accept-Language: uk,en;q=0.9,en-US;q=0.8,ru;q=0.7";
        $headers[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8";
        $headers[] = "Accept: application/json, text/javascript, /; q=0.01";
        $headers[] = "Cache-Control: no-cache";
        $headers[] = "X-Requested-With: XMLHttpRequest";
        $headers[] = "Cookie: PHPSESSID=3ft43d3…MG2Z6570";
        $headers[] = "Connection: keep-alive";
        $headers[] = "Referer: https://gl.ki.court.gov.ua/sud2601/gromadyanam/csz/";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close ($ch);

        $result = json_decode($result);
        return $result;
    }

    private function saveItemToDb($courtCode, $item)
    {
        $itemObj = new CourtSessionHelper($courtCode, $item);

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

class CourtSessionHelper
{
    public $courtSessionId;
    public $courtCode;
    public $date;
    public $number;//номер справи
    public $forma;
    public $involved;

    public $judgeId;
    public $titleId;

    public function __construct(int $courtCode, $item)
    {
        $this->courtCode = $courtCode;
        $this->date = date('Y-m-d', strtotime($item->date));
        $this->number = $item->number;
        $this->forma = $item->forma;
        $this->involved = $item->involved;

        $titleRaw = $item->description;

        if (!empty($this->caseId = CourtSession::getCourtSessionId($this->courtCode, $this->date, $this->number))) {
            return;
        }

        $judgeNamesParsedArr = [];
        $judgeNamesRawArr = explode(',', $item->judge);
        foreach ($judgeNamesRawArr as $judgeNameRaw) {
            $judgeNamesParsedArr[] = Judge::parseJudgeName($judgeNameRaw);
        }

        var_dump($judgeNamesParsedArr);die;

        $titleParsed = EssencesCase::parseTitle($titleRaw);
        $this->titleId = EssencesCase::fillIdByParsedTitle($titleParsed);

    }
}
