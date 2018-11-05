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

        //додаємо інформацію по закладкам в БД (судді)
        $judges_etalon_data = call_user_func_array(['Toecyd\Judge', 'select'], UserBookmarkJudge::getBookmarkFields())
            ->join('courts', 'judges.court', '=', 'courts.court_code')
            ->limit(2)
            ->get()
            ->all();

        foreach ($judges_etalon_data as $key => $row) {
            DB::table('user_bookmark_judges')->insert([
                'user'  => $this->user->id,
                'judge' => $row->id,
            ]);

            $judges_etalon_data[$key] = $row->toArray();
        }

        //додаємо інформацію по закладкам в БД (суди)
        $courts_etalon_data = (call_user_func_array(['Toecyd\Court', 'select'], UserBookmarkCourt::getBookmarkFields())
            ->leftJoin('instances', 'courts.instance_code', '=', 'instances.instance_code')
            ->leftJoin('regions', 'courts.region_code', '=', 'regions.region_code')
            ->leftJoin('jurisdictions', 'courts.jurisdiction', '=', 'jurisdictions.id')
            ->leftJoin('judges', 'courts.head_judge', '=', 'judges.id')
            ->limit(2)
            ->get()
            ->all()
        );

        foreach ($courts_etalon_data as $key => $row) {
            DB::table('user_bookmark_courts')->insert([
                'user'  => $this->user->id,
                'court' => $row->court_code,
            ]);

            $courts_etalon_data[$key] = $row->toArray();
        }

        // отримуємо закладки
        $response = $this->get($this->url, $headers_with_token);
        $response->assertStatus(200);

        $response_data = $response->decodeResponseJson();
        $this->assertEquals($judges_etalon_data, $response_data['judges']);
        $this->assertEquals($courts_etalon_data, $response_data['courts']);
    }
}