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
    public function setUp()
    {
        parent::setUp();

        $this->url .= 'user/bookmarks';

    }

    /**
     * Пробуємо отримати порожній список закладок у щойно створеного користувача.
     */
    public function testEmptyBookmarks()
    {
        $response = $this->get($this->url, $this->headersWithToken($this->login($this->user_data)));

        $response->assertStatus(200);

        $response_data = $response->decodeResponseJson();
        $this->assertEquals([], $response_data['judges']);
        $this->assertEquals([], $response_data['courts']);
    }

    /**
     * Додаємо користувачу закладки, а потім перевіряємо, що вони додались
     */
    public function testNonEmptyBookmarks()
    {
        // Авторизуємось
        $headers_with_token = $this->headersWithToken($this->login($this->user_data));

        $etalon_data = $this->getEtalonData();

        //додаємо інформацію по закладкам в БД (судді)
        foreach ($etalon_data['judges'] as $row) {
            UserBookmarkJudge::createBookmark($this->user->id, $row['id']);
        }

        //додаємо інформацію по закладкам в БД (суди)
        foreach ($etalon_data['courts'] as $key => $row) {
            UserBookmarkCourt::createBookmark($this->user->id, $row['court_code']);
        }

        // отримуємо закладки
        $response = $this->get($this->url, $headers_with_token);
        $response->assertStatus(200);

        $response_data = $response->decodeResponseJson();
        $this->assertEquals($etalon_data, $response_data);
    }

    /**
     * Додаємо інформацію, що дублюється, а потім перевіряємо, що з БД інформація дістається без дублів
     */
    public function testBookmarksDoubles()
    {
        // Авторизуємось
        $headers_with_token = $this->headersWithToken($this->login($this->user_data));

        $etalon_data = $this->getEtalonData();

        //додаємо інформацію по закладкам в БД (судді)
        foreach ($etalon_data['judges'] as $row) {
            UserBookmarkJudge::createBookmark($this->user->id, $row['id']);
            UserBookmarkJudge::createBookmark($this->user->id, $row['id']); // дубль
        }

        //додаємо інформацію по закладкам в БД (суди)
        foreach ($etalon_data['courts'] as $key => $row) {
            UserBookmarkCourt::createBookmark($this->user->id, $row['court_code']);
            UserBookmarkCourt::createBookmark($this->user->id, $row['court_code']); // дубль
        }

        // отримуємо закладки
        $response = $this->get($this->url, $headers_with_token);
        $response->assertStatus(200);

        $response_data = $response->decodeResponseJson();
        $this->assertEquals($etalon_data, $response_data);
    }

    private function getEtalonData()
    {
        $limit = 2;

        $result = [
            'judges' => call_user_func_array(['Toecyd\Judge', 'select'], UserBookmarkJudge::getBookmarkFields())
                ->join('courts', 'judges.court', '=', 'courts.court_code')
                ->limit($limit)
                ->get()
                ->all(),
            'courts' => call_user_func_array(['Toecyd\Court', 'select'], UserBookmarkCourt::getBookmarkFields())
                ->leftJoin('instances', 'courts.instance_code', '=', 'instances.instance_code')
                ->leftJoin('regions', 'courts.region_code', '=', 'regions.region_code')
                ->leftJoin('jurisdictions', 'courts.jurisdiction', '=', 'jurisdictions.id')
                ->leftJoin('judges', 'courts.head_judge', '=', 'judges.id')
                ->limit($limit)
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