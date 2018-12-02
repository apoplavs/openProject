<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Toecyd\Court;
use Toecyd\Judge;

class MysqlFunctionsTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

    }

    public function testGetJudgesById()
    {
        $court_code = (Court::getCourtCodes())[0];
        $max_judge_id = (int)Judge::max('id');

        $judges_rows = [
            ['id' => $max_judge_id + 1, 'court' => $court_code, 'surname' => 'Шевченко', 'name' => 'Тарас', 'patronymic' => 'Григорович'],
            ['id' => $max_judge_id + 2, 'court' => $court_code, 'surname' => 'Українка', 'name' => 'Л', 'patronymic' => 'П'],
            ['id' => $max_judge_id + 3, 'court' => $court_code, 'surname' => 'Франко', 'name' => 'Іван', 'patronymic' => 'Якович'],
        ];

        foreach ($judges_rows as $row) {
            Judge::create($row);
        }

        $test_cases = [
            [$max_judge_id + 1, 'NULL', 'NULL', 'Шевченко Тарас Григорович'],
            [$max_judge_id + 2, 'NULL', 'NULL', 'Українка Л. П.'],
            [$max_judge_id + 1, $max_judge_id + 2, 'NULL', 'головуючий суддя: Шевченко Тарас Григорович; Українка Л. П.'],
            [$max_judge_id + 1, $max_judge_id + 2, $max_judge_id + 3, 'головуючий суддя: Шевченко Тарас Григорович; учасник колегії: Українка Л. П.; учасник колегії: Франко Іван Якович'],
            [$max_judge_id + 2, 'NULL', $max_judge_id + 1, 'Українка Л. П.'],
            [$max_judge_id + 100500, 'NULL', 'NULL', null],
        ];

        foreach ($test_cases as $case) {
            $etalon_data = $case[3];
            $test_data = (DB::select(
                    DB::raw("SELECT get_judges_by_id({$case[0]}, {$case[1]}, {$case[2]}) AS result")
                ))[0]->result;
            $this->assertEquals($etalon_data, $test_data);
        }

        $this->assertTrue(true);
    }
}
