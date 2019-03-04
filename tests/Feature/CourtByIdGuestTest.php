<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Toecyd\Court;
use Toecyd\CourtSession;
use Toecyd\Judge;

class CourtByIdGuestTest extends BaseApiTest
{
    public function setUp() {
        parent::setUp();

        $this->url .= 'guest/courts';
    }

    public function getInsertDbData()
    {
        $court_code =  Court::max('court_code') + 1;

        $head_judge_id = Judge::max('id') + 1;
        $secondary_judge_id = $head_judge_id + 1;

        $court_session_max_id = CourtSession::max('id');

        return [
            'courts' => [
                'court_code' => $court_code,
                'name' => 'court_name',
                'instance_code' => 3,
                'region_code' => 26,
                'head_judge' => 1,
                'jurisdiction' => 1,
                'phone' => '098-765-43-21',
                'email' => 'test@example.com',
                'site' => 'example.com',
                'rating' => 2,
            ],
            'judges' => [
                [
                    'id' => $head_judge_id,
                    'court' => $court_code,
                    'surname' => 'Тестовий',
                    'name' => 'Головний',
                    'patronymic' => 'Суддя',
                    'address' => 'М. Київ, вул. Тестова, буд. 1, кімната 1'
                ],
                [
                    'id' => $secondary_judge_id,
                    'court' => $court_code,
                    'surname' => 'Тестовий',
                    'name' => 'Звичайний',
                    'patronymic' => 'Суддя',
                    'address' => 'М. Київ, вул. Тестова, буд. 2, кімната 2'
                ],
            ],
            'court_sessions' => [
                [
                    'id' => $court_session_max_id + 1,
                    'court' => $court_code,
                    'judge1' => $head_judge_id,
                    'judge2' => null,
                    'date' => date('Y-m-d', strtotime('+2 week')),
                    'forma' => 1,
                ],
                [
                    'id' => $court_session_max_id + 2,
                    'court' => $court_code,
                    'judge1' => $head_judge_id,
                    'judge2' => $secondary_judge_id,
                    'date' => date('Y-m-d', strtotime('+1 week')),
                    'forma' => 2,
                ],
            ]
        ];
    }

    public function getEtalonDataJudges($insert_db_data)
    {
        $result = DB::table('judges')
                    ->select('surname', 'name', 'patronymic', 'status', 'updated_status', 'due_date_status', 'rating', 'id', 'photo')
                    ->where('court', '=', $insert_db_data['courts']['court_code'])
                    ->orderBy('rating', 'DESC')
                    ->get()
                    ->toArray();

        $result = array_map(function ($obj) {return (array)$obj;}, $result);

        return $result;
    }

    public function getEtalonDataCourtSessions($insert_db_data)
    {
        $result = DB::table('court_sessions')
                    ->select(DB::raw('DATE_FORMAT(`court_sessions`.`date`, "%d.%m.%Y %H:%i") AS date'),
                             DB::raw('get_judges_by_id(judge1, judge2, judge3) AS judges'),
                             DB::raw('justice_kinds.name AS forma'),
                             'number', 'involved', 'description')
                    ->join('justice_kinds', 'justice_kinds.justice_kind', '=', 'court_sessions.forma')
                    ->where('court', '=', $insert_db_data['courts']['court_code'])
                    ->orderBy('date', 'ASC')
                    ->get()
                    ->toArray();

        $result = array_map(function ($obj) {return (array)$obj;}, $result);

        return $result;
    }

    public function getEtalonData($insert_db_data)
    {
        $etalon_data = $insert_db_data['courts'];

        unset($etalon_data['instance_code']);
        unset($etalon_data['region_code']);

        $etalon_data['instance'] = DB::table('instances')->where('instance_code', '=', $insert_db_data['courts']['instance_code'])->value('name');
        $etalon_data['region'] = DB::table('regions')->where('region_code', '=', $insert_db_data['courts']['region_code'])->value('name');
        $etalon_data['jurisdiction'] = DB::table('jurisdictions')->where('id', '=', $insert_db_data['courts']['jurisdiction'])->value('title');
        $etalon_data['head_judge'] = DB::table('judges')->where('id', '=', $insert_db_data['courts']['head_judge'])->value(DB::raw('get_one_judge_by_id(id)'));

        $etalon_data['address'] = array_unique(array_map(function ($judge) {return $judge['address'];}, $insert_db_data['judges']));

        $etalon_data['judges'] = $this->getEtalonDataJudges($insert_db_data);

        $etalon_data['court_sessions'] = $this->getEtalonDataCourtSessions($insert_db_data);

        return $etalon_data;
    }

    public function testBasic()
    {
        $insert_db_data = $this->getInsertDbData();
        $this->insertDataToDb($insert_db_data);
        $etalon_data = $this->getEtalonData($insert_db_data);

        $response = $this->get($this->url . "/{$etalon_data['court_code']}", $this->headersWithToken($this->login($this->user_data)));

        $response->assertStatus(200);
        $test_data = $response->decodeResponseJson();
        $this->assertEquals($etalon_data, $test_data);
    }

    public function testNonUniqueAddresses()
    {
        $insert_db_data = $this->getInsertDbData();
        $insert_db_data['judges'][1]['address'] = $insert_db_data['judges'][0]['address'];
        $this->insertDataToDb($insert_db_data);
        $etalon_data = $this->getEtalonData($insert_db_data);

        $response = $this->get($this->url . "/{$etalon_data['court_code']}", $this->headersWithToken($this->login($this->user_data)));

        $response->assertStatus(200);
        $test_data = $response->decodeResponseJson();
        $this->assertEquals($etalon_data, $test_data);
    }
}
