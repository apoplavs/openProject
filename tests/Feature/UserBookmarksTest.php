<?php

namespace Tests\Feature;

use Toecyd\Court;
use Toecyd\Judge;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\TestResponse;
use Toecyd\UserBookmarkCourt;
use Toecyd\UserBookmarkJudge;

class UserBookmarksTest extends BaseApiTest
{
    const LIMIT = 2;

    const IDENT_TO_FIELD = [
        'judges' => 'id',
        'courts' => 'court_code',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->url .= 'user/bookmarks';
    }

    public function assertBookmarksEquals($etalon_data, $headers_with_token)
    {
        $response = $this->get($this->url, $headers_with_token);
        $response->assertStatus(200);
        $this->assertEquals($etalon_data, $response->decodeResponseJson());
    }

    public function putBookmarks($etalon_data, $headers_with_token, $etalonStatus = 201)
    {
        foreach ($etalon_data as $ident => $rows) {
            foreach ($rows as $row) {
                $this->put("api/v1/{$ident}/{$row[self::IDENT_TO_FIELD[$ident]]}/bookmark", [], $headers_with_token)
                    ->assertStatus($etalonStatus);
            }
        }
    }

    public function deleteBookmarks($etalon_data, $headers_with_token)
    {
        foreach ($etalon_data as $ident => $rows) {
            foreach ($rows as $row) {
                $this
                    ->delete("api/v1/{$ident}/{$row[self::IDENT_TO_FIELD[$ident]]}/bookmark", [], $headers_with_token)
                    ->assertStatus(204);
            }
        }
    }

    public function testAll()
    {
        $etalon_data = $this->getEtalonData();
        $etalon_data_empty = array_map(function() {return [];}, $etalon_data);
        $headers_with_token = $this->headersWithToken($this->login($this->user_data));

        // Перевіряємо, що зразу ж після додавання користувача його закладки порожні
        $this->assertBookmarksEquals($etalon_data_empty, $headers_with_token);

        // Перевіряємо, що закладки успішно додаються
        $this->putBookmarks($etalon_data, $headers_with_token);
        $this->assertBookmarksEquals($etalon_data, $headers_with_token);

        // Перевіряємо, що закладки успішно видаляються
        $this->deleteBookmarks($etalon_data, $headers_with_token);
        $this->assertBookmarksEquals($etalon_data_empty, $headers_with_token);

        // Перевіряємо, що закладки не дублюються
        $this->putBookmarks($etalon_data, $headers_with_token, 201);
        $this->putBookmarks($etalon_data, $headers_with_token, 422); // дубль
        $this->assertBookmarksEquals($etalon_data, $headers_with_token);
    }

    private function getEtalonData()
    {
        $result = [
            'judges' => call_user_func_array(['Toecyd\Judge', 'select'], UserBookmarkJudge::getBookmarkFields())
                ->join('courts', 'judges.court', '=', 'courts.court_code')
                ->limit(self::LIMIT)
                ->get()
                ->all(),
            'courts' => call_user_func_array(['Toecyd\Court', 'select'], UserBookmarkCourt::getBookmarkFields())
                ->leftJoin('instances', 'courts.instance_code', '=', 'instances.instance_code')
                ->leftJoin('regions', 'courts.region_code', '=', 'regions.region_code')
                ->leftJoin('jurisdictions', 'courts.jurisdiction', '=', 'jurisdictions.id')
                ->leftJoin('judges', 'courts.head_judge', '=', 'judges.id')
                ->limit(self::LIMIT)
                ->get()
                ->all(),
        ];

        // Застосовуємо toArray() до кожного рядка даних з результату
        foreach ($result as $key => $rows) {
            $result[$key] = array_map(function ($row) {return $row->toArray();}, $rows);
        }

        return $result;
    }
}