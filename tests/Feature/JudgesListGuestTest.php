<?php

namespace Tests\Feature;

use Toecyd\Judge;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\TestResponse;

class JudgesListGuestTest extends BaseApiTest
{
    public function setUp() {
        parent::setUp();

        $this->url .= 'guest/judges/list';
    }

    /**
     * @return array
     */
    public function getAllRegions()
    {
        return range(2, 26);
    }

    /**
     * @return array
     */
    public function getAllInstances()
    {
        return range(1,3);
    }

    /**
     * @return array
     */
    public function getAllJurisdictions()
    {
        return range(1,3);
    }

    /**
     * @param TestResponse $response
     *
     * @return mixed
     */
    public function getJudgesFromResponse(TestResponse $response)
    {
        $response->assertStatus(200);

        return ($response->decodeResponseJson())['data'];
    }

    /**
     * @return array
     */
    protected function getFieldstoSelect()
    {
        return Judge::getJudgesListGuestFields();
    }

    /**
     * @param bool  $powers_expired
     * @param array $orderBy
     *
     * @return Builder
     */
    public function getJudgesQuery(bool $powers_expired = false, array $orderBy = ['judges.id', 'ASC']) : Builder
    {
        return Judge::select($this->getFieldstoSelect())
            // якщо не переданий аргумент щоб показувати суддів в яких закінчились повноваження - значить упускємо їх при вибірці
            ->when(!$powers_expired, function (Builder $query) {
                return $query->where('judges.status', '!=', 5);
            })
            ->join('courts', 'judges.court', '=', 'courts.court_code')
            ->orderBy($orderBy[0], $orderBy[1]);
    }

    /**
     * Базовий тест: пробуємо отримати список суддів, не передавши ніяких параметрів
     */
    public function testJudgesList()
    {
        $judges_etalon_query = $this->getJudgesQuery();
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get($this->url, $this->headersWithToken($this->login($this->user_data)));

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Отримання списку суддів з указанням параметра regions
     *
     * @param $regions:array
     *
     * @dataProvider regionSetsProvider
     */
    public function testRegions(array $regions) {
        $judges_etalon_query = $this->getJudgesQuery()->whereIn('courts.region_code', $regions);
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['regions' => $regions]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * @return array
     */
    public function regionSetsProvider()
    {
        return [
            [[2]],
            [[3]],
            [[2, 3]],
            [$this->getAllRegions()],
        ];
    }

    /**
     * Перевіряємо, що при передачі порожнього списку регіонів виведуться всі судді
     */
    public function testRegionsEmpty() {
        $judges_etalon_query = $this->getJudgesQuery();
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['regions' => []]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевіряємо, що при передачі повного списку регіонів виведуться всі судді
     */
    public function testRegionsAll() {
        $judges_etalon_query = $this->getJudgesQuery();
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['regions' => $this->getAllRegions()]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевіряємо, що при передачі неіснуючого номера регіона виведеться помилка
     */
    public function testRegionsError() {
        $this->get(
            $this->url . '?' . http_build_query(['regions' => [max($this->getAllRegions()) + 1]]),
            $this->headersWithToken($this->login($this->user_data))
        )->assertStatus(422);
    }

    /**
     * Отримання списку суддів з указанням параметра instances
     *
     * @param array $instances
     *
     * @dataProvider instancesSetsProvider
     */
    public function testInstances(array $instances) {
        $judges_etalon_query = $this->getJudgesQuery()->whereIn('courts.instance_code', $instances);
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['instances' => $instances]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * @return array
     */
    public function instancesSetsProvider()
    {
        return [
            [[1]],
            [[2]],
            [[1, 2]],
            [[2, 3]],
            [$this->getAllInstances()],
        ];
    }

    /**
     * Перевіряємо, що при порожньому списку інстанцій виведеться весь список суддів
     */
    public function testInstancesEmpty() {
        $judges_etalon_query = $this->getJudgesQuery();
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['instances' => []]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевіряємо, що при повному списку інстанцій виведеться весь список суддів
     */
    public function testInstancesAll() {
        $judges_etalon_query = $this->getJudgesQuery();
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['instances' => $this->getAllInstances()]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевіряємо, що при завеликому номері інстанції виведеться помилка
     */
    public function testInstancesError() {
        $this->get(
            $this->url . '?' . http_build_query(['instances' => [max($this->getAllInstances()) + 1]]),
            $this->headersWithToken($this->login($this->user_data))
        )->assertStatus(422);
    }

    /**
     *  Отримання списку суддів з указанням параметра jurisdictions
     *
     * @param array $jurisdictions
     *
     * @dataProvider jurisdictionsSetsProvider
     */
    public function testJurisdictions(array $jurisdictions) {
        $judges_etalon_query = $this->getJudgesQuery()->whereIn('courts.jurisdiction', $jurisdictions);
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['jurisdictions' => $jurisdictions]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * @return array
     */
    public function jurisdictionsSetsProvider()
    {
        return [
            [[1]],
            [[2]],
            [[1, 2]],
            [[2, 3]],
            [$this->getAllInstances()],
        ];
    }

    /**
     * Перевіряємо, що при порожньому списку юрисдикцій виведеться весь список суддів
     */
    public function testJurisdictionsEmpty() {
        $judges_etalon_query = $this->getJudgesQuery();
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['jurisdictions' => []]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевіряємо, що при повному списку юрисдикцій виведеться весь список суддів
     */
    public function testJurisdictionsAll() {
        $judges_etalon_query = $this->getJudgesQuery();
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['jurisdictions' => $this->getAllJurisdictions()]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевіряємо, що при завеликому номері юрисдикції виведеться помилка
     */
    public function testJurisdictionsError() {
        $this->get(
            $this->url . '?' . http_build_query(['jurisdictions' => [max($this->getAllJurisdictions()) + 1]]),
            $this->headersWithToken($this->login($this->user_data))
        )->assertStatus(422);
    }

    /**
     * Отримання списку суддів з указанням параметра search
     */
    public function testSearch()
    {
        $search = 'Ков';

        $judges_etalon_query = $this->getJudgesQuery()->where('judges.surname', 'LIKE', $search . '%');
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['search' => $search]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевіряємо, що для неіснуючого search буде повернуто порожній масив суддів
     */
    public function testSearchNotFound()
    {
        $search = str_repeat('Ы', 20);

        $judges_etalon = [];

        $response = $this->get(
            $this->url . '?' . http_build_query(['search' => $search]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевіряємо, що пошук по порожньому рядку не буде здійснено
     */
    public function testSearchEmpty()
    {
        $response = $this->get(
            $this->url . '?' . http_build_query(['search' => '']),
            $this->headersWithToken($this->login($this->user_data))
        )->assertStatus(422);
    }

    /**
     * Перевіряємо, що пошук по рядку не з букв не буде здійснено
     */
    public function testSearchNonAlpha()
    {
        $response = $this->get(
            $this->url . '?' . http_build_query(['search' => '%']),
            $this->headersWithToken($this->login($this->user_data))
        )->assertStatus(422);
    }

    /**
     * Отримання списку суддів з указанням сортування
     *
     * @param int $sort
     *
     * @dataProvider sortProvider
     */
    public function testSort(int $sort)
    {
        $judges_etalon_query = $this->getJudgesQuery(false, (Judge::getSortVariants())[$sort]);
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['sort' => $sort]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * @return array
     */
    public function sortProvider() {
        $result = [];

        foreach (array_keys(Judge::getSortVariants()) as $key) {
            $result[] = [$key];
        }

        return $result;
    }

    /**
     * Перевірка, що при невірному сортуванні отримаємо помилку
     */
    public function testSortIncorrect()
    {
        $response = $this->get(
            $this->url . '?' . http_build_query(['sort' => max(array_keys(Judge::getSortVariants())) + 1]),
            $this->headersWithToken($this->login($this->user_data))
        )->assertStatus(422);
    }

    /**
     * Отримання списку суддів з указанням параметра powers_expired
     *
     * @param      $powers_expired_test
     * @param bool $powers_expired_etalon
     *
     * @dataProvider powersExpiredProvider
     */
    public function testPowersExpired($powers_expired_test, bool $powers_expired_etalon)
    {
        $judges_etalon_query = $this->getJudgesQuery($powers_expired_etalon);
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['expired' => $powers_expired_test]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * @return array
     */
    public function powersExpiredProvider()
    {
        return [
            [1, true],
            [2, true],
            ['yes', true],
            [true, true],
            [0, false],
            ['', false],
            [[], false],
            [null, false],
        ];
    }


    /**
     * Перевірка отримання першої сторінки пагінації
     */
    public function testPageFirst()
    {
        $page = 1;

        $judges_etalon_query = $this->getJudgesQuery();
        $judges_etalon = ($judges_etalon_query->paginate(
            Judge::JUDGES_PER_PAGE,
            ['*'],
            'page',
            $page
        )->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['page' => $page]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевірка отримання останньої сторінки пагінації
     */
    public function testPageLast()
    {
        $judges_etalon_query = $this->getJudgesQuery();
        $page = $judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->lastPage();
        $judges_etalon = ($judges_etalon_query->paginate(
            Judge::JUDGES_PER_PAGE,
            ['*'],
            'page',
            $page
        )->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['page' => $page]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевірка отримання сторінки пагінації, взятої з середини
     */
    public function testPageMiddle()
    {
        $judges_etalon_query = $this->getJudgesQuery();
        $page = floor($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->lastPage() / 2);
        $judges_etalon = ($judges_etalon_query->paginate(
            Judge::JUDGES_PER_PAGE,
            ['*'],
            'page',
            $page
        )->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query(['page' => $page]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевірка того, що при вказанні завеликої сторінки пагінації отримаємо порожній масив суддів
     */
    public function testPageTooBig()
    {
        $judges_etalon_query = $this->getJudgesQuery();
        $page = $judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->lastPage() + 1;
        $judges_etalon = [];

        $response = $this->get(
            $this->url . '?' . http_build_query(['page' => $page]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевірка валідації: при від'ємній сторінці пагінації маємо отримати помилку
     */
    public function testPageMinus()
    {
        $response = $this->get(
            $this->url . '?' . http_build_query(['page' => -1]),
            $this->headersWithToken($this->login($this->user_data))
        )->assertStatus(422);
    }

    /**
     * Перевірка валідації: якщо номер сторінки пагінації є нецілим числом, то маємо отримати помилку
     */
    public function testPageNonInteger()
    {
        $response = $this->get(
            $this->url . '?' . http_build_query(['page' => 'aaa']),
            $this->headersWithToken($this->login($this->user_data))
        )->assertStatus(422);
    }

    /**
     * Перевірка фільтрації по двом параметрам одразу: regions, instances
     */
    public function testRegionsInstances()
    {
        $regions = [2];
        $instances = [2];

        $judges_etalon_query = $this->getJudgesQuery()
            ->whereIn('courts.region_code', $regions)
            ->whereIn('courts.instance_code', $instances);
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query([
                    'regions' => $regions,
                    'instances' => $instances
            ]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевірка фільтрації по двом параметрам одразу: regions, jurisdictions
     */
    public function testRegionsJurisdictions()
    {
        $regions = [2];
        $jurisdictions = [2];

        $judges_etalon_query = $this->getJudgesQuery()
            ->whereIn('courts.region_code', $regions)
            ->whereIn('courts.jurisdiction', $jurisdictions);
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query([
                'regions' => $regions,
                'jurisdictions' => $jurisdictions
            ]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевірка фільтрації по двом параметрам одразу: regions, search
     */
    public function testRegionsSearch()
    {
        $regions = [2];
        $search = 'Ков';

        $judges_etalon_query = $this->getJudgesQuery()
            ->whereIn('courts.region_code', $regions)
            ->where('judges.surname', 'LIKE', $search . '%');
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query([
                'regions' => $regions,
                'search' => $search
            ]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевірка фільтрації по двом параметрам одразу: sort, search
     */
    public function testSortSearch()
    {
        $sort = 2;
        $search = 'Ков';

        $judges_etalon_query = $this->getJudgesQuery(false, (Judge::getSortVariants())[$sort])
            ->where('judges.surname', 'LIKE', $search . '%');
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query([
                'sort' => $sort,
                'search' => $search
            ]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }

    /**
     * Перевірка фільтрації по двом параметрам одразу: instance, expired
     */
    public function testInstancePowersExpired()
    {
        $instances = [1,2];
        $powers_expired = 1;

        $judges_etalon_query = $this->getJudgesQuery($powers_expired)
            ->whereIn('courts.instance_code', $instances);
        $judges_etalon = ($judges_etalon_query->paginate(Judge::JUDGES_PER_PAGE)->toArray())['data'];

        $response = $this->get(
            $this->url . '?' . http_build_query([
                'instances' => $instances,
                'expired' => $powers_expired
            ]),
            $this->headersWithToken($this->login($this->user_data))
        );

        $this->assertEquals($judges_etalon, $this->getJudgesFromResponse($response));
    }
}