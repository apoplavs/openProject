<?php

namespace Tests\Feature;

use Toecyd\Court;
use Toecyd\Judge;
use Illuminate\Http\UploadedFile;

class JudgesPhotoTest extends BaseApiTest
{
    public function setUp()
    {
        parent::setUp();
        $this->url .= 'judges/photo';
    }

    public function getInsertDbData()
    {
        $court_code =  Court::max('court_code') + 1;

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
                    'id' => Judge::max('id') + 1,
                    'court' => $court_code,
                    'surname' => 'Тестовий',
                    'name' => 'Головний',
                    'patronymic' => 'Суддя',
                    'address' => 'М. Київ, вул. Тестова, буд. 1, кімната 1'
                ],
            ],
        ];
    }

    public function testBadJudgeId()
    {
        $insert_db_data = $this->getInsertDbData();
        $insert_db_data['judges'][0]['photo'] = '/img/judges/yes_photo.jpg';
        $judge_id = $insert_db_data['judges'][0]['id'];
        $this->insertDataToDb($insert_db_data);
        $response = $this->post($this->url, ['judge_id' => $judge_id + 1], $this->headersWithToken($this->login($this->user_data)));
        $response->assertStatus(422);
    }

    public function testPhotoAlreadyExists()
    {
        $insert_db_data = $this->getInsertDbData();
        $judge_id = $insert_db_data['judges'][0]['id'];
        $this->insertDataToDb($insert_db_data);
        $response = $this->post($this->url, ['judge_id' => $judge_id + 1], $this->headersWithToken($this->login($this->user_data)));
        $response->assertStatus(422);
    }

    public function testRequestWithoutPhoto()
    {
        $insert_db_data = $this->getInsertDbData();
        $judge_id = $insert_db_data['judges'][0]['id'];
        $this->insertDataToDb($insert_db_data);
        $response = $this->post($this->url, ['judge_id' => $judge_id], $this->headersWithToken($this->login($this->user_data)));
        $response->assertStatus(422);
    }


    public function testTrivial()
    {
        $insert_db_data = $this->getInsertDbData();
        $this->insertDataToDb($insert_db_data);
        $judge_id = $insert_db_data['judges'][0]['id'];
        $response = $this->post($this->url, ['judge_id' => $judge_id, 'photo' => UploadedFile::fake()->image('photo.jpg')->size($this->getMaxPhotoSizeInKb())], $this->headersWithToken($this->login($this->user_data)));
        $this->assertCorrectResponse($response);
    }

    private function getMaxPhotoSizeInKb()
    {
        return pow(2, 10);
    }
}