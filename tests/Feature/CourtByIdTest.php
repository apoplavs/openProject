<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Toecyd\Court;
use Toecyd\Judge;

class CourtByIdTest extends CourtByIdGuestTest{
    public function setUp() {
        parent::setUp();

        $this->url = str_replace('guest/courts', 'courts', $this->url);
    }

    public function getEtalonData($insert_db_data)
    {
        $etalon_data = parent::getEtalonData($insert_db_data);

        // додаємо поле "is_bookmark" зі значенням 0. Там, де це поле має дорівнювати 1, будемо виставляти його руками, для більшої прозорості тестів.
        $etalon_data['is_bookmark'] = 0;

        return $etalon_data;
    }

    public function getEtalonDataJudges($insert_db_data)
    {
        $result = parent::getEtalonDataJudges($insert_db_data);

        foreach ($result as &$row) {
            // додаємо поле "is_bookmark" зі значенням 0. Там, де це поле має дорівнювати 1, будемо виставляти його руками, для більшої прозорості тестів.
            $row['is_bookmark'] = 0;
        }

        return $result;
    }

    public function getEtalonDataCourtSessions($insert_db_data)
    {
        $result = parent::getEtalonDataCourtSessions($insert_db_data);

        foreach ($result as &$row) {
            // додаємо поле "is_bookmark" зі значенням 0. Там, де це поле має дорівнювати 1, будемо виставляти його руками, для більшої прозорості тестів.
            $row['is_bookmark'] = 0;
        }

        return $result;    }

    public function testWithCourtInBookmarks()
    {
        $insert_db_data = $this->getInsertDbData();
        $insert_db_data['user_bookmark_courts'] = [['user' => $this->user->id, 'court' => $insert_db_data['courts']['court_code']]];
        $this->insertDataToDb($insert_db_data);

        $etalon_data = $this->getEtalonData($insert_db_data);
        $etalon_data['is_bookmark'] = 1;

        $response = $this->get($this->url . "/{$etalon_data['court_code']}", $this->headersWithToken($this->login($this->user_data)));

        $response->assertStatus(200);
        $test_data = $response->decodeResponseJson();
        $this->assertEquals($etalon_data, $test_data);
    }

    public function testWithJudgeInBookmarks()
    {
        $insert_db_data = $this->getInsertDbData();
        $insert_db_data['user_bookmark_judges'] = [['user' => $this->user->id, 'judge' => $insert_db_data['judges'][0]['id']]];
        $this->insertDataToDb($insert_db_data);

        $etalon_data = $this->getEtalonData($insert_db_data);
        $etalon_data['judges'][0]['is_bookmark'] = 1;

        $response = $this->get($this->url . "/{$etalon_data['court_code']}", $this->headersWithToken($this->login($this->user_data)));

        $response->assertStatus(200);
        $test_data = $response->decodeResponseJson();
        $this->assertEquals($etalon_data, $test_data);
    }

    public function testWithSessionInBookmarks()
    {
        $insert_db_data = $this->getInsertDbData();
        $insert_db_data['user_bookmark_sessions'] = [['user' => $this->user->id, 'court_session' => $insert_db_data['court_sessions'][0]['id']]];
        $this->insertDataToDb($insert_db_data);

        $etalon_data = $this->getEtalonData($insert_db_data);
        $etalon_data['court_sessions'][0]['is_bookmark'] = 1;

        $response = $this->get($this->url . "/{$etalon_data['court_code']}", $this->headersWithToken($this->login($this->user_data)));

        $response->assertStatus(200);
        $test_data = $response->decodeResponseJson();
        $this->assertEquals($etalon_data, $test_data);
    }
}
