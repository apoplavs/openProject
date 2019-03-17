<?php

namespace Toecyd\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Toecyd\CourtSession;
use Toecyd\Http\Controllers\Controller;
use Toecyd\Jobs\SendNotification1;
use Toecyd\Jobs\SendNotification3;
use Toecyd\Judge;
use Toecyd\JudgesStatistic;
use Toecyd\UserBookmarkJudge;
use Toecyd\UserHistory;
use Toecyd\UsersLikesJudge;
use Toecyd\UsersUnlikesJudge;
use Aws\S3\S3Client;
use DateTime;
use Carbon\Carbon;

/**
 * Class JudgesController
 * @package Toecyd\Http\Controllers\Judges
 */
class JudgesController extends Controller
{
     const TIMEZONE = 'Europe/Kiev';
     const AWS_S3 = 'https://s3.eu-central-1.amazonaws.com/toecyd/';
    /**
     * Display a listing of the resource.
     *
     * @SWG\Get(
     *     path="/judges/list",
     *     summary="Отримати список суддів",
     *     description="Отримати список суддів за заданими параметрами можна передавши параметри (фільтри) пошуку описані нижче. Всі параметри можна використовувати як окремо, так і суміщати декілька параметрів одночасно. Всі результати пошуку повертаються по 10 шт. на одній сторінці",
     *     operationId="judges-list",
     *     produces={"application/json"},
     *     tags={"Судді"},
     *     security={
     *     {"passport": {}},
     *      },
     *     @SWG\Parameter(
     *      ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *      ref="#/parameters/X-Requested-With",
     *     ),
     *
     *     @SWG\Parameter(
     *     name="regions[]",
     *     in="query",
     *     description="Перелік регіонів (областей), в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись по всіх регіонах. Коди регіонів:
     *   2 - Вінницька область
     *   3 - Волинська область
     *   4 - Дніпропетровська область
     *   5 - Донецька область
     *   6 - Житомирська область
     *   7 - Закарпатська область
     *   8 - Запорізька область
     *   9 - Івано-Франківська область
     *   10 - Київська область
     *   11 - Кіровоградська область
     *   12 - Луганська область
     *   13 - Львівська область
     *   14 - Миколаївська область
     *   15 - Одеська область
     *   16 - Полтавська область
     *   17 - Рівненська область
     *   18 - Сумська область
     *   19 - Тернопільська область
     *   20 - Харківська область
     *   21 - Херсонська область
     *   22 - Хмельницька область
     *   23 - Черкаська область
     *   24 - Чернівецька область
     *   25 - Чернігівська область
     *   26 - м. Київ
    НАПРИКЛАД: 'host/api/v1/judges/list?regions[]=2&regions[]=3&regions[]=4' - означає, що потрібно отримати всіх суддів з Вінницької, Волинської і Дніпропетровської областей",
     *     type="array",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     @SWG\Items(
     *            type="integer",
     *            example="7"
     *          )
     *     ),
     *
     *    @SWG\Parameter(
     *     name="instances[]",
     *     in="query",
     *     description="Інстанції судів в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись у всіх інстанціях. Коди інстанцій:
     *   1 - Касаційна інстанція
     *   2 - Апеляційна інстанція
     *   3 - Перша інстанція",
     *     type="array",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     @SWG\Items(
     *            type="integer",
     *            example="2"
     *          )
     *     ),
     *
     *    @SWG\Parameter(
     *     name="jurisdictions[]",
     *     in="query",
     *     description="Юрисдикції судів в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись у всіх юрисдикціях. Коди юрисдикцій:
     *   1 - Загальна юрисдикція суду
     *   2 - Адміністративна юрисдикція суду
     *   3 - Господарська юрисдикція суду",
     *     type="array",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     @SWG\Items(
     *            type="integer",
     *            example="1"
     *          )
     *     ),
     *
     *    @SWG\Parameter(
     *     name="search",
     *     in="query",
     *     description="Пошук за прізвищем судді. Повинен містити від 1 до 20 символів. Будуть повернуті всі судді початок прізвища яких співпадає з заданим параметром.
    НАПРИКЛАД: 'host/api/v1/judges/list?search=Кова' - означає, що потрібно отримати всіх суддів прізвище яких починається на 'Кова%'",
     *     type="string",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minLength=1,
     *     maxLength=20,
     *     allowEmptyValue=false
     *     ),
     *
     *    @SWG\Parameter(
     *     name="sort",
     *     in="query",
     *     description="Тип сортування при поверненні результатів. Коди типів:
     *   1 - Сортувати за прізвищем 'А->Я'
     *   2 - Сортувати за прізвищем 'Я->А'
     *   3 - Сортувати за рейтингом 'низький->високий'
     *   4 - Сортувати за рейтингом 'високий->низький'",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     maximum=4,
     *     allowEmptyValue=false
     *     ),
     *
     *    @SWG\Parameter(
     *     name="expired",
     *     in="query",
     *     description="Якщо переданий цей параметр із значенням > 0, то в результати пошуку будуть включені судді зі статусом 'закінчились повноваження'",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     default=1,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Оскільки результати відображаються по 10 шт. на сторінці, даний параметр вказує номер сторінки яку потрібно отримати",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="ОК",
     *         @SWG\Schema(
     *         @SWG\Property(property="current_page", type="integer", description="Поточна сторінка пошуку"),
     *         @SWG\Property(property="data", type="json", description="Список суддів"),
     *         @SWG\Property(property="id", type="string", description="id судді"),
     *         @SWG\Property(property="court_name", type="string", description="Назва суду, в якому даний суддя працює"),
     *         @SWG\Property(property="surname", type="string", description="Прізвище судді"),
     *         @SWG\Property(property="name", type="string", description="Ім'я судді"),
     *         @SWG\Property(property="patronymic", type="string", description="По батькові судді"),
     *         @SWG\Property(property="photo", type="string", description="URL фото судді"),
     *         @SWG\Property(property="status", type="integer", description="Id поточного статусу судді
     *   1 - суддя на роботі
     *   2 - На лікарняному
     *   3 - У відпустці
     *   4 - Відсуній на робочому місці з інших причин
     *   5 - Припинено повноваження"),
     *         @SWG\Property(property="updated_status", type="string", description="Дата останнього оновлення статусу"),
     *         @SWG\Property(property="due_date_status", type="string", description="Дата дії статусу (до якого часу даний статус буде діяти)"),
     *         @SWG\Property(property="rating", type="integer", description="Місце в рейтингу серед усіх суддів"),
     *         @SWG\Property(property="is_bookmark", type="integer", description="Чи знаходиться в закладках в поточного користувача 1 - так,  0 - ні"),
     *         @SWG\Property(property="from", type="integer", description="Загальний номер результату в пошуку, який є першим на даній сторінці"),
     *         @SWG\Property(property="last_page", type="integer", description="Остання сторінка в пошуку"),
     *         @SWG\Property(property="path", type="string", description="URL поточного ресурсу"),
     *         @SWG\Property(property="per_page", type="integer", description="Кількість результатів на поточній сторінці"),
     *         @SWG\Property(property="total", type="integer", description="Загальна кількість результатів"),
     *         @SWG\Property(property="to", type="integer", description="Загальний номер результату в пошуку, який є останнім на даній сторінці"),
     *         ),
     *         examples={"application/json":
     *              {
     *                  "current_page": 1,
     *                  "data": {
     *                  {
     *                  "id": 9087,
     *                  "court_name": "Господарський суд Київської області",
     *                  "surname": "Євграфова",
     *                  "name": "Є",
     *                  "patronymic": "П",
     *                  "photo": "/img/judges/no_photo.jpg",
     *                  "status": 1,
     *                  "updated_status": "08.06.2018",
     *                  "due_date_status": null,
     *                  "rating": 0,
     *                  "is_bookmark": 0
     *                  },
     *                  {
     *                  "id": 1518,
     *                  "court_name": "Шосткинський міськрайонний суд Сумської області",
     *                  "surname": "Євдокімова",
     *                  "name": "Олена",
     *                  "patronymic": "Павлівна",
     *                  "photo": "/img/judges/no_photo.jpg",
     *                  "status": 3,
     *                  "updated_status": "21.07.2018",
     *                  "due_date_status": "13.06.2018",
     *                  "rating": 0,
     *                  "is_bookmark": 0
     *                  },
     *                  {
     *                  "id": 5793,
     *                  "court_name": "Соснівський районний суд м. Черкаси",
     *                  "surname": "Євтушенко",
     *                  "name": "П",
     *                  "patronymic": "М",
     *                  "photo": "/img/judges/no_photo.jpg",
     *                  "status": 1,
     *                  "updated_status": "23.05.2018",
     *                  "due_date_status": null,
     *                  "rating": 0,
     *                  "is_bookmark": 0
     *                  }
     *                  },
     *                  "first_page_url": "http://toecyd.top/api/v1/judges/list?page=1",
     *                  "from": 1,
     *                  "last_page": 745,
     *                  "last_page_url": "http://toecyd.top/api/v1/judges/list?page=745",
     *                  "next_page_url": "http://toecyd.top/api/v1/judges/list?page=2",
     *                  "path": "http://toecyd.local/api/v1/judges/list",
     *                  "per_page": 10,
     *                  "prev_page_url": null,
     *                  "to": 10,
     *                  "total": 7445
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=401,
     *         description="Необхідна аутентифікація користувача, можливо токен не існує, або анульований",
     *         examples={"application/json":
     *              {
     *                  "message": "Unauthenticated",
     *              }
     *          }
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується GET.",
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, у відповіді буде зазначена причина",
     *         examples={"application/json":
     *              {
     *                  "message": "The given data was invalid.",
     *                  "errors": {
     *                      "regions.0": {
     *                                "максимальне значення для regions.0 = 26."
     *                      },
     *                      "instances.0": {
     *                                 "мінімальне значення для instances.0 = 1."
     *                      },
     *                      "instances.1": {
     *                                  "максимальне значення для instances.1 = 3."
     *                      }
     *                  }
     *              }
     *          }
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        // валідація фільтрів
        $request->validate([
            'regions' => 'array',
            'regions.*' => 'numeric|min:2|max:26',
            'instances' => 'array',
            'instances.*' => 'numeric|min:1|max:3',
            'jurisdictions' => 'array',
            'jurisdictions.*' => 'numeric|min:1|max:3',
            'search' => 'string|alpha|min:1|max:20',
            'sort' => 'numeric|min:1|max:4',
            'page' => 'int|min:0',
        ]);

        // приведення фільтрів до коректного вигляду
        $filters = $this->getFilters();
        // отримання результатів
        $judges_list = Judge::getJudgesList($filters['regions'], $filters['instances'], $filters['jurisdictions'],
            $filters['sort_order'], $filters['search'], $filters['powers_expired']);

        return response()->json($judges_list);
    }


    /**
     * @SWG\Get(
     *     path="/guest/judges/list",
     *     summary="Отримати список суддів для незареєстрованого користувача",
     *     description="Даний маршрут працює так же як /judges/list, за винятком того, що не вимагає авторизації користувача і не повертає даних що стосуються користувача (напр. is_bookmark)",
     *     operationId="guest-judges-list",
     *     produces={"application/json"},
     *     tags={"Судді"},
     *     @SWG\Parameter(
     *      ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *      ref="#/parameters/X-Requested-With",
     *     ),
     *
     *     @SWG\Parameter(
     *     name="regions[]",
     *     in="query",
     *     description="Перелік регіонів (областей), в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись по всіх регіонах. Коди регіонів:
     *   2 - Вінницька область
     *   3 - Волинська область
     *   4 - Дніпропетровська область
     *   5 - Донецька область
     *   6 - Житомирська область
     *   7 - Закарпатська область
     *   8 - Запорізька область
     *   9 - Івано-Франківська область
     *   10 - Київська область
     *   11 - Кіровоградська область
     *   12 - Луганська область
     *   13 - Львівська область
     *   14 - Миколаївська область
     *   15 - Одеська область
     *   16 - Полтавська область
     *   17 - Рівненська область
     *   18 - Сумська область
     *   19 - Тернопільська область
     *   20 - Харківська область
     *   21 - Херсонська область
     *   22 - Хмельницька область
     *   23 - Черкаська область
     *   24 - Чернівецька область
     *   25 - Чернігівська область
     *   26 - м. Київ
    НАПРИКЛАД: 'host/api/v1/judges/list?regions[]=2&regions[]=3&regions[]=4' - означає, що потрібно отримати всіх суддів з Вінницької, Волинської і Дніпропетровської областей",
     *     type="array",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     @SWG\Items(
     *            type="integer",
     *            example="7"
     *          )
     *     ),
     *
     *    @SWG\Parameter(
     *     name="instances[]",
     *     in="query",
     *     description="Інстанції судів в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись у всіх інстанціях. Коди інстанцій:
     *   1 - Касаційна інстанція
     *   2 - Апеляційна інстанція
     *   3 - Перша інстанція",
     *     type="array",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     @SWG\Items(
     *            type="integer",
     *            example="2"
     *          )
     *     ),
     *
     *    @SWG\Parameter(
     *     name="jurisdictions[]",
     *     in="query",
     *     description="Юрисдикції судів в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись у всіх юрисдикціях. Коди юрисдикцій:
     *   1 - Загальна юрисдикція суду
     *   2 - Адміністративна юрисдикція суду
     *   3 - Господарська юрисдикція суду",
     *     type="array",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     @SWG\Items(
     *            type="integer",
     *            example="1"
     *          )
     *     ),
     *
     *    @SWG\Parameter(
     *     name="search",
     *     in="query",
     *     description="Пошук за прізвищем судді. Повинен містити від 1 до 20 символів. Будуть повернуті всі судді початок прізвища яких співпадає з заданим параметром.
    НАПРИКЛАД: 'host/api/v1/judges/list?search=Кова' - означає, що потрібно отримати всіх суддів прізвище яких починається на 'Кова%'",
     *     type="string",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minLength=1,
     *     maxLength=20,
     *     allowEmptyValue=false
     *     ),
     *
     *    @SWG\Parameter(
     *     name="sort",
     *     in="query",
     *     description="Тип сортування при поверненні результатів. Коди типів:
     *   1 - Сортувати за прізвищем 'А->Я'
     *   2 - Сортувати за прізвищем 'Я->А'
     *   3 - Сортувати за рейтингом 'низький->високий'
     *   4 - Сортувати за рейтингом 'високий->низький'",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     maximum=4,
     *     allowEmptyValue=false
     *     ),
     *
     *    @SWG\Parameter(
     *     name="expired",
     *     in="query",
     *     description="Якщо переданий цей параметр із значенням > 0, то в результати пошуку будуть включені судді зі статусом 'закінчились повноваження'",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     default=1,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Оскільки результати відображаються по 10 шт. на сторінці, даний параметр вказує номер сторінки яку потрібно отримати",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="ОК",
     *         @SWG\Schema(
     *         @SWG\Property(property="current_page", type="integer", description="Поточна сторінка пошуку"),
     *         @SWG\Property(property="data", type="json", description="Список суддів"),
     *         @SWG\Property(property="id", type="string", description="id судді"),
     *         @SWG\Property(property="court_name", type="string", description="Назва суду, в якому даний суддя працює"),
     *         @SWG\Property(property="surname", type="string", description="Прізвище судді"),
     *         @SWG\Property(property="name", type="string", description="Ім'я судді"),
     *         @SWG\Property(property="patronymic", type="string", description="По батькові судді"),
     *         @SWG\Property(property="photo", type="string", description="URL фото судді"),
     *         @SWG\Property(property="status", type="integer", description="Id поточного статусу судді
     *   1 - суддя на роботі
     *   2 - На лікарняному
     *   3 - У відпустці
     *   4 - Відсуній на робочому місці з інших причин
     *   5 - Припинено повноваження"),
     *         @SWG\Property(property="updated_status", type="string", description="Дата останнього оновлення статусу"),
     *         @SWG\Property(property="due_date_status", type="string", description="Дата дії статусу (до якого часу даний статус буде діяти)"),
     *         @SWG\Property(property="rating", type="integer", description="Місце в рейтингу серед усіх суддів"),
     *         @SWG\Property(property="from", type="integer", description="Загальний номер результату в пошуку, який є першим на даній сторінці"),
     *         @SWG\Property(property="last_page", type="integer", description="Остання сторінка в пошуку"),
     *         @SWG\Property(property="path", type="string", description="URL поточного ресурсу"),
     *         @SWG\Property(property="per_page", type="integer", description="Кількість результатів на поточній сторінці"),
     *         @SWG\Property(property="total", type="integer", description="Загальна кількість результатів"),
     *         @SWG\Property(property="to", type="integer", description="Загальний номер результату в пошуку, який є останнім на даній сторінці"),
     *         ),
     *         examples={"application/json":
     *              {
     *                  "current_page": 1,
     *                  "data": {
     *                  {
     *                  "id": 4012,
     *                  "court_name": "Господарський суд Київської області",
     *                  "surname": "Євграфова",
     *                  "name": "Є",
     *                  "patronymic": "П",
     *                  "photo": "/img/judges/no_photo.jpg",
     *                  "status": 1,
     *                  "updated_status": "08.06.2018",
     *                  "due_date_status": null,
     *                  "rating": 0
     *                  },
     *                  {
     *                  "id": 114,
     *                  "court_name": "Шосткинський міськрайонний суд Сумської області",
     *                  "surname": "Євдокімова",
     *                  "name": "Олена",
     *                  "patronymic": "Павлівна",
     *                  "photo": "/img/judges/no_photo.jpg",
     *                  "status": 3,
     *                  "updated_status": "21.07.2018",
     *                  "due_date_status": "13.06.2018",
     *                  "rating": 0
     *                  },
     *                  {
     *                  "id": 1518,
     *                  "court_name": "Соснівський районний суд м. Черкаси",
     *                  "surname": "Євтушенко",
     *                  "name": "П",
     *                  "patronymic": "М",
     *                  "photo": "/img/judges/no_photo.jpg",
     *                  "status": 1,
     *                  "updated_status": "23.05.2018",
     *                  "due_date_status": null,
     *                  "rating": 0
     *                  }
     *                  },
     *                  "first_page_url": "http://toecyd.top/api/v1/judges/list?page=1",
     *                  "from": 1,
     *                  "last_page": 745,
     *                  "last_page_url": "http://toecyd.top/api/v1/judges/list?page=745",
     *                  "next_page_url": "http://toecyd.top/api/v1/judges/list?page=2",
     *                  "path": "http://toecyd.local/api/v1/judges/list",
     *                  "per_page": 10,
     *                  "prev_page_url": null,
     *                  "to": 10,
     *                  "total": 7445
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується GET.",
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, у відповіді буде зазначена причина",
     *         examples={"application/json":
     *              {
     *                  "message": "The given data was invalid.",
     *                  "errors": {
     *                      "search": {
     *                                "search може містити лише літери."
     *                      },
     *                      "instances.0": {
     *                                 "максимальне значення для instances.0 = 3."
     *                      },
     *                      "instances.1": {
     *                                  "instances.1 повинен бути числом."
     *                      }
     *                  }
     *              }
     *          }
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function indexGuest(Request $request)
    {
        // валідація фільтрів
        $request->validate([
            'regions' => 'array',
            'regions.*' => 'numeric|min:2|max:26',
            'instances' => 'array',
            'instances.*' => 'numeric|min:1|max:3',
            'jurisdictions' => 'array',
            'jurisdictions.*' => 'numeric|min:1|max:3',
            'search' => 'string|alpha|min:1|max:20',
            'sort' => 'numeric|min:1|max:4',
            'page' => 'int|min:0',
        ]);
        // приведення фільтрів до коректного вигляду
        $filters = $this->getFilters();
        // отримання результатів
        $judges_list = Judge::getJudgesListGuest($filters['regions'], $filters['instances'], $filters['jurisdictions'],
            $filters['sort_order'], $filters['search'], $filters['powers_expired']);
        return response()->json($judges_list);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }










    /**
     *
     * @SWG\Get(
     *     path="/judges/{id}",
     *     summary="Дані про певного суддю",
     *     description="Отримати дані про суддю, id якого передано в параметрах, якщо будь-які дані будуть невідомі, значення відповідного параметра буде NULL",
     *     operationId="judges-id",
     *     produces={"application/json"},
     *     tags={"Судді"},
     *     security={
     *     {"passport": {}},
     *      },
     *     @SWG\Parameter(
     *      ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *      ref="#/parameters/X-Requested-With",
     *     ),
     *    @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Id судді, про якого потрібно отримати дані",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     maximum=15000,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="ОК",
     *         @SWG\Schema(
     *          @SWG\Property(
     *              property="data",
     *              description="Ідентифікаційні дані про поточного суддю",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(property="id", type="integer", description="id судді"),
     *                  @SWG\Property(property="surname", type="string", description="Прізвище судді"),
     *                  @SWG\Property(property="name", type="string", description="Ім'я судді"),
     *                  @SWG\Property(property="patronymic", type="string", description="По батькові судді"),
     *                  @SWG\Property(property="photo", type="string", description="URL фото судді"),
     *                  @SWG\Property(property="status", type="integer", description="Id поточного статусу судді
     *                      1 - суддя на роботі
     *                      2 - На лікарняному
     *                      3 - У відпустці
     *                      4 - Відсуній на робочому місці з інших причин
     *                      5 - Припинено повноваження"),
     *                  @SWG\Property(property="updated_status", type="string", description="Дата останнього оновлення статусу"),
     *                  @SWG\Property(property="due_date_status", type="string", description="Дата дії статусу (до якого часу даний статус буде діяти)"),
     *                  @SWG\Property(property="rating", type="integer", description="Місце в рейтингу серед усіх суддів"),
     *                  @SWG\Property(property="court_name", type="string", description="Назва суду, в якому даний суддя працює"),
     *                  @SWG\Property(property="court_address", type="string", description="Адреса суду в якому працює судя на дний момент"),
     *                  @SWG\Property(property="court_phone", type="string", description="Телефон суду"),
     *                  @SWG\Property(property="court_email", type="string", description="email суду"),
     *                  @SWG\Property(property="court_site", type="string", description="Посилання на сайт суду"),
     *                  @SWG\Property(property="likes", type="integer", description="Загальна кількість лайків усіх користувачів для даного судді"),
     *                  @SWG\Property(property="unlikes", type="integer", description="Загальна кількість дизлайків усіх користувачів для даного судді"),
     *                  @SWG\Property(property="is_liked", type="integer", description="1 || 0 відображає чи поставив поточний користувач лайк даному судді"),
     *                  @SWG\Property(property="is_unliked", type="integer", description="1 || 0 відображає чи поставив поточний користувач дизлайк даному судді"),
     *                  @SWG\Property(property="is_bookmark", type="integer", description="1 || 0 відображає чи знаходиться даний суддя в закладках в поточного користувача"),
     *              )
     *          ),
     *
     *         @SWG\Property(
     *              property="previous_works",
     *              type="array",
     *              description="Список попередніх місць роботи розташований в хронологічному порядку, (якщо суддя раніше працював в інших судах), або пустий масив",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(property="court_name", type="string", description="Назва суду, в якому даний суддя працював раніше"),
     *                  ),
     *              ),
     *
     *         @SWG\Property(
     *              property="court_sessions",
     *              type="array",
     *              description="Список судових засідань даного судді розташований в хронологічному порядку",
     *              @SWG\Items(
     *                  type="object",
	 *     				@SWG\Property(property="id", type="integer", description="Id засідання"),
     *                  @SWG\Property(property="date", type="string", description="Час та дата розгляду"),
     *                  @SWG\Property(property="number", type="string", description="Номер справи"),
     *                  @SWG\Property(property="judges", type="string", description="Склад суду"),
	 *     				@SWG\Property(property="fоrma", type="string", description="Форма судочинства [Цивільне, Кримінальне ...]"),
     *                  @SWG\Property(property="involved", type="string", description="Сторони у справі"),
     *                  @SWG\Property(property="description", type="string", description="Суть справи"),
     *                  @SWG\Property(property="is_bookmark", type="integer", description="1 || 0 відображає чи знаходяться судові засідання по даній справі в закладках в поточного користувача"),
     *              ),
     *            ),
	 *     		@SWG\Property(
	 *              property="common_statistic",
	 *              type="array",
	 *              description="Загальна зведена статистика",
	 *              @SWG\Items(
	 *                  type="object",
	 *     				required={"competence", "timeliness"},
	 *     				@SWG\Property(property="competence", type="integer", description="Показує скільки відсотків рішень даного судді вистояли у вищих інстанціях"),
	 *                  @SWG\Property(property="timeliness", type="integer", description="Показує скільки відсотків проваджень були розглянуті в визначений законом строк"),
	 *              ),
	 *            ),
	 *     		@SWG\Property(
	 *              property="adminoffence_statistic",
	 *              type="array",
	 *              description="Статистика розглянутих суддею справ в порядку КУпАП",
	 *              @SWG\Items(
	 *                  type="object",
	 *     				required={"amount"},
	 *     				@SWG\Property(property="amount", type="integer", description="Загальна кількість справ, розглянутих суддею"),
	 *                  @SWG\Property(property="positive_judgment", type="integer", description="Відсоток постанов в яких особу звільнено від відповідальності"),
	 *                  @SWG\Property(property="negative_judgment", type="integer", description="Відсоток постанов в яких особу притягнено до відповідальності"),
	 *                  @SWG\Property(property="cases_on_time", type="integer", description="Відсоток справ розглянутих у визначені законом строки"),
	 *     				@SWG\Property(property="cases_not_on_time", type="integer", description="Відсоток розглянутих справ з порушенням строків визначених законодавством"),
	 *                  @SWG\Property(property="average_duration", type="integer", description="Кількість днів, що відображає середню тривалість розгляду однієї справи"),
	 *     				@SWG\Property(property="approved_by_appeal", type="integer", description="Відсоток постанов, які встояли в апеляції"),
	 *     				@SWG\Property(property="not_approved_by_appeal", type="integer", description="Відсоток постанов, які НЕ встояли в апеляції"),
	 *              ),
	 *            ),
	 *     		@SWG\Property(
	 *              property="criminal_statistic",
	 *              type="array",
	 *              description="Статистика розглянутих суддею кримінальних справ",
	 *              @SWG\Items(
	 *                  type="object",
	 *     				required={"amount"},
	 *     				@SWG\Property(property="amount", type="integer", description="Загальна кількість справ, розглянутих суддею"),
	 *                  @SWG\Property(property="positive_judgment", type="integer", description="Відсоток вироків в яких особу звільнено від відповідальності"),
	 *                  @SWG\Property(property="negative_judgment", type="integer", description="Відсоток вироків в яких особу притягнено до відповідальності"),
	 *                  @SWG\Property(property="cases_on_time", type="integer", description="Відсоток справ розглянутих у визначені законом строки"),
	 *     				@SWG\Property(property="cases_not_on_time", type="integer", description="Відсоток розглянутих справ з порушенням строків визначених законодавством"),
	 *                  @SWG\Property(property="average_duration", type="integer", description="Кількість днів, що відображає середню тривалість розгляду однієї справи"),
	 *     				@SWG\Property(property="approved_by_appeal", type="integer", description="Відсоток справ, які встояли в апеляції"),
	 *     				@SWG\Property(property="not_approved_by_appeal", type="integer", description="Відсоток справ, які НЕ встояли в апеляції"),
	 *              ),
	 *            ),
	 *     		@SWG\Property(
	 *              property="civil_statistic",
	 *              type="array",
	 *              description="Статистика розглянутих суддею цивільних справ",
	 *              @SWG\Items(
	 *                  type="object",
	 *     				required={"amount"},
	 *     				@SWG\Property(property="amount", type="integer", description="Загальна кількість справ, розглянутих суддею"),
	 *                  @SWG\Property(property="positive_judgment", type="integer", description="Відсоток справ в яких задоволено вимоги позивача"),
	 *                  @SWG\Property(property="negative_judgment", type="integer", description="Відсоток справ в яких відмовлено у задоволенні вимог позивача"),
	 *    				@SWG\Property(property="other_judgment", type="integer", description="Відсоток справ, в яких укладено мирову угоду, або закрито справу"),
	 *                  @SWG\Property(property="cases_on_time", type="integer", description="Відсоток справ розглянутих у визначені законом строки"),
	 *     				@SWG\Property(property="cases_not_on_time", type="integer", description="Відсоток розглянутих справ з порушенням строків визначених законодавством"),
	 *                  @SWG\Property(property="average_duration", type="integer", description="Кількість днів, що відображає середню тривалість розгляду однієї справи"),
	 *     				@SWG\Property(property="approved_by_appeal", type="integer", description="Відсоток рішень, які встояли в апеляції"),
	 *     				@SWG\Property(property="not_approved_by_appeal", type="integer", description="Відсоток рішень, які НЕ встояли в апеляції"),
	 *              ),
	 *            ),
     *          ),
     *         examples={"application/json":
     *              {
     *                  "data": {
	 *							"id": 12054,
	 *							"surname": "Наполов",
	 *							"name": "Микола",
	 *							"patronymic": "Іванович",
	 *							"photo": "/img/judges/no_photo.jpg",
	 *							"status": 1,
	 *							"updated_status": "15.11.2018",
	 *							"due_date_status": null,
	 *							"rating": 0,
	 *							"court_name": "Ніжинський міськрайонний суд Чернігівської області",
	 *							"court_address": "16600, м. Ніжин, вул. Шевченка, 57 а",
	 *							"court_phone": "(04631) 7-55-29",
	 *							"court_email": "inbox@ng.cn.court.gov.ua",
	 *							"court_site": "https://ng.cn.court.gov.ua",
	 *							"likes": 427,
	 *							"unlikes": 20,
	 *							"is_liked": 1,
	 *							"is_unliked": 0,
	 *							"is_bookmark": 0
	 *							},
	 *							"previous_works": {
	 *								"Новгород-Сіверський районний суд Чернігівської області"
	 *							},
	 *     						"court_sessions": {
	 *								{
	 *     							"id": 12457,
	 *								"date": "2018-11-18 12:30:00",
	 *								"number": "752/12199/18",
	 *								"judges": "головуючий суддя: Воробйов Андрій Володимирович; учасник колегії: Ходько В М; учасник колегії: Наполов Микола Іванович",
	 *								"forma": "Кримінальне",
	 *								"involved": "",
	 *								"description": "",
	 *								"is_bookmark": 0
	 *								},
	 *								{
	 *     							"id": 42527,
	 *								"date": "2018-11-19 14:00:00",
	 *								"number": "752/20790/18",
	 *								"judges": "Наполов Микола Іванович",
	 *								"forma": "Цивільне",
	 *								"involved": "Позивач: АТ КБ БАНК, відповідач: Шевченко Іван Іваноич",
	 *								"description": "про стягнення заборгованості",
	 *								"is_bookmark": 0
	 *								},
	 *     						},
	 *     						"common_statistic": {
	 *								"competence": 65,
	 *								"timeliness": 85
	 *							},
	 *							"adminoffence_statistic": {
	 *								"amount": 1111,
	 *								"positive_judgment": 9,
	 *								"negative_judgment": 91,
	 *								"cases_on_time": 84,
	 *								"cases_not_on_time": 16,
	 *								"average_duration": 11
	 *							},
	 * 							"civil_statistic": {
	 *								"amount": 1598,
	 *								"positive_judgment": 76,
	 *								"negative_judgment": 4,
	 *								"other_judgment": 20,
	 *								"cases_on_time": 79,
	 *								"cases_not_on_time": 21,
	 *								"average_duration": 57,
	 *								"approved_by_appeal": 57,
	 * 								"not_approved_by_appeal": 43
	 *							},
	 *							"criminal_statistic": {
	 *								"amount": 552,
	 *								"positive_judgment": 17,
	 *								"negative_judgment": 83,
	 *								"cases_on_time": 93,
	 *								"cases_not_on_time": 7,
	 *								"average_duration": 87,
	 *								"approved_by_appeal": 73,
	 *								"not_approved_by_appeal": 27
	 * 							},
	 *							"admin_statistic": {
	 * 								"amount": 111
	 * 							},
	 *							"commercial_statistic": {
	 *								"amount": 0
	 *							}
     *              	}
	 *	 		}
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, у відповіді буде зазначена причина",
     *         examples={"application/json":
     *              {
     *                  "message": "Неіснуючий id судді!"
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується GET.",
     *     )
     * )
     */
    public function show($id) {
        $data = Judge::getJudgeData($id);
        $previous_works = [];
        $last_work = $data['previous_work'];

        while ($last_work) {
            $pw = Judge::getPreviousWork($last_work);
            $previous_works[] = $pw['court_name'];
            $last_work = $pw['previous_work'];
        }
        $court_sessions = CourtSession::getSessionByJudge($id);

        // отимуємо статистику по типах справ
		$adminoffence_statistic = JudgesStatistic::getAdminoffenceStatistic($id);
		$civil_statistic = 	JudgesStatistic::getCivilStatistic($id);
		$criminal_statistic = JudgesStatistic::getCriminalStatistic($id);
		$admin_statistic = JudgesStatistic::getAdminStatistic($id);
		$commercial_statistic = JudgesStatistic::getCommercialStatistic($id);

		// рахуємо загальну статистику
		$common_statistic = $this->countCommonStatistic($adminoffence_statistic, $criminal_statistic, $civil_statistic);

        // вносим в історію переглядів
        if (Auth::check()) {
        UserHistory::addToHistory($id);
        }

        return response()->json([
            'data' => $data,
            'previous_works' => $previous_works,
            'court_sessions' => $court_sessions,
			'common_statistic' => $common_statistic,
			'adminoffence_statistic' => $adminoffence_statistic,
			'civil_statistic' => $civil_statistic,
			'criminal_statistic' => $criminal_statistic,
			'admin_statistic' => $admin_statistic,
			'commercial_statistic' => $commercial_statistic
        ]);
    }


	/**
	 *
	 * @SWG\Get(
	 *     path="/guest/judges/{id}",
	 *     summary="Дані про певного суддю для незареєстрованого користувача",
	 *     description="Даний маршрут працює так же як /judges/{id}, за винятком того, що не вимагає авторизації користувача і не повертає даних що стосуються користувача",
	 *     operationId="guest-judges-id",
	 *     produces={"application/json"},
	 *     tags={"Судді"},
	 *     @SWG\Parameter(
	 *      ref="#/parameters/Content-Type",
	 *     ),
	 *     @SWG\Parameter(
	 *      ref="#/parameters/X-Requested-With",
	 *     ),
	 *    @SWG\Parameter(
	 *     name="id",
	 *     in="path",
	 *     required=true,
	 *     description="Id судді, про якого потрібно отримати дані",
	 *     type="integer",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     minimum=1,
	 *     maximum=15000,
	 *     allowEmptyValue=false
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=200,
	 *         description="ОК",
	 *         @SWG\Schema(
	 *          @SWG\Property(
	 *              property="data",
	 *              description="Ідентифікаційні дані про поточного суддю",
	 *              type="array",
	 *              @SWG\Items(
	 *                  type="object",
	 *                  @SWG\Property(property="id", type="integer", description="id судді"),
	 *                  @SWG\Property(property="surname", type="string", description="Прізвище судді"),
	 *                  @SWG\Property(property="name", type="string", description="Ім'я судді"),
	 *                  @SWG\Property(property="patronymic", type="string", description="По батькові судді"),
	 *                  @SWG\Property(property="photo", type="string", description="URL фото судді"),
	 *                  @SWG\Property(property="status", type="integer", description="Id поточного статусу судді
	 *                      1 - суддя на роботі
	 *                      2 - На лікарняному
	 *                      3 - У відпустці
	 *                      4 - Відсуній на робочому місці з інших причин
	 *                      5 - Припинено повноваження"),
	 *                  @SWG\Property(property="updated_status", type="string", description="Дата останнього оновлення статусу"),
	 *                  @SWG\Property(property="due_date_status", type="string", description="Дата дії статусу (до якого часу даний статус буде діяти)"),
	 *                  @SWG\Property(property="rating", type="integer", description="Місце в рейтингу серед усіх суддів"),
	 *                  @SWG\Property(property="court_name", type="string", description="Назва суду, в якому даний суддя працює"),
	 *                  @SWG\Property(property="court_address", type="string", description="Адреса суду в якому працює судя на дний момент"),
	 *                  @SWG\Property(property="court_phone", type="string", description="Телефон суду"),
	 *                  @SWG\Property(property="court_email", type="string", description="email суду"),
	 *                  @SWG\Property(property="court_site", type="string", description="Посилання на сайт суду"),
	 *                  @SWG\Property(property="likes", type="integer", description="Загальна кількість лайків усіх користувачів для даного судді"),
	 *              )
	 *          ),
	 *
	 *         @SWG\Property(
	 *              property="previous_works",
	 *              type="array",
	 *              description="Список попередніх місць роботи розташований в хронологічному порядку, (якщо суддя раніше працював в інших судах), або пустий масив",
	 *              @SWG\Items(
	 *                  type="object",
	 *                  @SWG\Property(property="court_name", type="string", description="Назва суду, в якому даний суддя працював раніше"),
	 *                  ),
	 *              ),
	 *
	 *         @SWG\Property(
	 *              property="court_sessions",
	 *              type="array",
	 *              description="Список судових засідань даного судді розташований в хронологічному порядку",
	 *              @SWG\Items(
	 *                  type="object",
	 *                  @SWG\Property(property="date", type="string", description="Час та дата розгляду"),
	 *                  @SWG\Property(property="number", type="string", description="Номер справи"),
	 *                  @SWG\Property(property="judges", type="string", description="Склад суду"),
	 *     				@SWG\Property(property="fоrma", type="string", description="Форма судочинства [Цивільне, Кримінальне ...]"),
	 *                  @SWG\Property(property="involved", type="string", description="Сторони у справі"),
	 *                  @SWG\Property(property="description", type="string", description="Суть справи"),
	 *              ),
	 *            ),
	 *     		@SWG\Property(
	 *              property="adminoffence_statistic",
	 *              type="array",
	 *              description="Статистика розглянутих суддею справ в порядку КУпАП",
	 *              @SWG\Items(
	 *                  type="object",
	 *     				required={"amount"},
	 *     				@SWG\Property(property="amount", type="integer", description="Загальна кількість справ, розглянутих суддею"),
	 *                  @SWG\Property(property="positive_judgment", type="integer", description="Відсоток постанов в яких особу звільнено від відповідальності"),
	 *                  @SWG\Property(property="negative_judgment", type="integer", description="Відсоток постанов в яких особу притягнено до відповідальності"),
	 *                  @SWG\Property(property="cases_on_time", type="integer", description="Відсоток справ розглянутих у визначені законом строки"),
	 *     				@SWG\Property(property="cases_not_on_time", type="integer", description="Відсоток розглянутих справ з порушенням строків визначених законодавством"),
	 *                  @SWG\Property(property="average_duration", type="integer", description="Кількість днів, що відображає середню тривалість розгляду однієї справи"),
	 *     				@SWG\Property(property="approved_by_appeal", type="integer", description="Відсоток постанов, які встояли в апеляції"),
	 *     				@SWG\Property(property="not_approved_by_appeal", type="integer", description="Відсоток постанов, які НЕ встояли в апеляції"),
	 *              ),
	 *            ),
	 *     		@SWG\Property(
	 *              property="criminal_statistic",
	 *              type="array",
	 *              description="Статистика розглянутих суддею кримінальних справ",
	 *              @SWG\Items(
	 *                  type="object",
	 *     				required={"amount"},
	 *     				@SWG\Property(property="amount", type="integer", description="Загальна кількість справ, розглянутих суддею"),
	 *                  @SWG\Property(property="positive_judgment", type="integer", description="Відсоток вироків в яких особу звільнено від відповідальності"),
	 *                  @SWG\Property(property="negative_judgment", type="integer", description="Відсоток вироків в яких особу притягнено до відповідальності"),
	 *                  @SWG\Property(property="cases_on_time", type="integer", description="Відсоток справ розглянутих у визначені законом строки"),
	 *     				@SWG\Property(property="cases_not_on_time", type="integer", description="Відсоток розглянутих справ з порушенням строків визначених законодавством"),
	 *                  @SWG\Property(property="average_duration", type="integer", description="Кількість днів, що відображає середню тривалість розгляду однієї справи"),
	 *     				@SWG\Property(property="approved_by_appeal", type="integer", description="Відсоток справ, які встояли в апеляції"),
	 *     				@SWG\Property(property="not_approved_by_appeal", type="integer", description="Відсоток справ, які НЕ встояли в апеляції"),
	 *              ),
	 *            ),
	 *     		@SWG\Property(
	 *              property="civil_statistic",
	 *              type="array",
	 *              description="Статистика розглянутих суддею цивільних справ",
	 *              @SWG\Items(
	 *                  type="object",
	 *     				required={"amount"},
	 *     				@SWG\Property(property="amount", type="integer", description="Загальна кількість справ, розглянутих суддею"),
	 *                  @SWG\Property(property="positive_judgment", type="integer", description="Відсоток справ в яких задоволено вимоги позивача"),
	 *                  @SWG\Property(property="negative_judgment", type="integer", description="Відсоток справ в яких відмовлено у задоволенні вимог позивача"),
	 *    				@SWG\Property(property="other_judgment", type="integer", description="Відсоток справ, в яких укладено мирову угоду, або закрито справу"),
	 *                  @SWG\Property(property="cases_on_time", type="integer", description="Відсоток справ розглянутих у визначені законом строки"),
	 *     				@SWG\Property(property="cases_not_on_time", type="integer", description="Відсоток розглянутих справ з порушенням строків визначених законодавством"),
	 *                  @SWG\Property(property="average_duration", type="integer", description="Кількість днів, що відображає середню тривалість розгляду однієї справи"),
	 *     				@SWG\Property(property="approved_by_appeal", type="integer", description="Відсоток рішень, які встояли в апеляції"),
	 *     				@SWG\Property(property="not_approved_by_appeal", type="integer", description="Відсоток рішень, які НЕ встояли в апеляції"),
	 *              ),
	 *            ),
	 *          ),
	 *         examples={"application/json":
	 *              {
	 *                  "data": {
	 *							"id": 12054,
	 *							"surname": "Наполов",
	 *							"name": "Микола",
	 *							"patronymic": "Іванович",
	 *							"photo": "/img/judges/no_photo.jpg",
	 *							"status": 1,
	 *							"updated_status": "15.11.2018",
	 *							"due_date_status": null,
	 *							"rating": 0,
	 *							"court_name": "Ніжинський міськрайонний суд Чернігівської області",
	 *							"court_address": "16600, м. Ніжин, вул. Шевченка, 57 а",
	 *							"court_phone": "(04631) 7-55-29",
	 *							"court_email": "inbox@ng.cn.court.gov.ua",
	 *							"court_site": "https://ng.cn.court.gov.ua",
	 *							"likes": 427,
	 *							"unlikes": 20
	 *							},
	 *							"previous_works": {
	 *								"Новгород-Сіверський районний суд Чернігівської області"
	 *							},
	 *     						"court_sessions": {
	 *								{
	 *								"date": "2018-11-18 12:30:00",
	 *								"number": "752/12199/18",
	 *								"judges": "головуючий суддя: Воробйов Андрій Володимирович; учасник колегії: Ходько В М; учасник колегії: Наполов Микола Іванович",
	 *								"forma": "Кримінальне",
	 *								"involved": "",
	 *								"description": ""
	 *								},
	 *								{
	 *								"date": "2018-11-19 14:00:00",
	 *								"number": "752/20790/18",
	 *								"judges": "Наполов Микола Іванович",
	 *								"forma": "Цивільне",
	 *								"involved": "Позивач: АТ КБ БАНК, відповідач: Шевченко Іван Іваноич",
	 *								"description": "про стягнення заборгованості"
	 *								},
	 *     						}
	 *              	}
	 *	 		}
	 *     ),
	 *     @SWG\Response(
	 *         response=422,
	 *         description="Передані не валідні дані, у відповіді буде зазначена причина",
	 *         examples={"application/json":
	 *              {
	 *                  "message": "Неіснуючий id судді!"
	 *              }
	 *          }
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=405,
	 *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується GET.",
	 *     )
	 * )
	 */
	public function showGuest($id) {
		$data = Judge::getJudgeDataGuest($id);
		$previous_works = [];
		$last_work = $data['previous_work'];

		while ($last_work) {
			$pw = Judge::getPreviousWork($last_work);
			$previous_works[] = $pw['court_name'];
			$last_work = $pw['previous_work'];
		}
		$court_sessions = CourtSession::getSessionByJudgeGuest($id);

		// отимуємо статистику по типах справ
		$adminoffence_statistic = JudgesStatistic::getAdminoffenceStatistic($id);
		$civil_statistic = 	JudgesStatistic::getCivilStatistic($id);
		$criminal_statistic = JudgesStatistic::getCriminalStatistic($id);
		$admin_statistic = JudgesStatistic::getAdminStatistic($id);
		$commercial_statistic = JudgesStatistic::getCommercialStatistic($id);

		// рахуємо загальну статистику
		$common_statistic = $this->countCommonStatistic($adminoffence_statistic, $criminal_statistic, $civil_statistic);


		return response()->json([
			'data' => $data,
			'previous_works' => $previous_works,
			'court_sessions' => $court_sessions,
			'common_statistic' => $common_statistic,
			'adminoffence_statistic' => $adminoffence_statistic,
			'civil_statistic' => $civil_statistic,
			'criminal_statistic' => $criminal_statistic,
			'admin_statistic' => $admin_statistic,
			'commercial_statistic' => $commercial_statistic
		]);
	}














    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     *
     * @SWG\Get(
     *     path="/judges/autocomplete",
     *     summary="Перелік ПІБ суддів для поля автодоповнення",
     *     description="Швидко отримати перелік ПІБ суддів для 'живого пошуку' Всі результати пошуку повертаються по 5 шт.",
     *     operationId="judges-autocomplete",
     *     produces={"application/json"},
     *     tags={"Судді"},
     *     @SWG\Parameter(
     *      ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *      ref="#/parameters/X-Requested-With",
     *     ),
     *
     *    @SWG\Parameter(
     *     name="search",
     *     in="query",
     *     description="Пошук за прізвищем судді. Повинен містити від 1 до 20 символів. Будуть повернуті всі судді, початок прізвища яких співпадає з заданим параметром.
    НАПРИКЛАД: 'host/api/v1/judges/autocomplete?search=Мельн' - означає, що потрібно отримати суддів прізвище яких починається на 'Мельн%'",
     *     type="string",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     required=true,
     *     minLength=1,
     *     maxLength=20,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="ОК",
     *         @SWG\Schema(
     *         @SWG\Property(property="id", type="integer", description="id судді"),
     *         @SWG\Property(property="surname", type="string", description="Прізвище судді"),
     *         @SWG\Property(property="name", type="string", description="Ім'я судді"),
     *         @SWG\Property(property="patronymic", type="string", description="По батькові судді"),
     *         ),
     *         examples={"application/json":
     *              {
     *                  {
     *                      "id": 1518,
     *                      "surname": "Борсук",
     *                      "name": "Петро",
     *                      "patronymic": "Павлович"
     *                      },
     *                      {
     *                      "id": 1620,
     *                      "surname": "Коваленко",
     *                      "name": "Валентина",
     *                      "patronymic": "Петрівна"
     *                      },
     *                      {
     *                      "id": 12518,
     *                      "surname": "Петрова",
     *                      "name": "О",
     *                      "patronymic": "Ф"
     *                      },
     *                      {
     *                      "id": 23182,
     *                      "surname": "Галинчук",
     *                      "name": "Володимир",
     *                      "patronymic": "Петрович"
     *                      },
     *                      {
     *                      "id": 18864,
     *                      "surname": "Борисенко",
     *                      "name": "Петро",
     *                      "patronymic": "Іванович"
     *                      }
     *                  }
     *              }
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, у відповіді буде зазначена причина",
     *         examples={"application/json":
     *              {
     *                  "message": "The given data was invalid.",
     *                  "errors": {
     *                      "search": {
     *                              "search може містити лише літери."
     *                      }
     *                  }
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується GET.",
     *     )
     * )
     */
    public function autocomplete(Request $request) {
        $search = Input::has('search') ? trim(Input::get('search')) : '';
        if (strlen($search) < 1 || !preg_match("/^[а-яєіїґ'-]+$/iu", $search)) {
            return response()->json([]);
        }

        // валідація фільтрів
        $request->validate([
            'search' => 'string|min:1|max:20'
        ]);
        // приведення першої букви в верхній регістр для валідного пошуку
        $search = mb_convert_case($search, MB_CASE_TITLE, "UTF-8");
        $autocomplete = Judge::getAutocomplete($search);
        return response()->json($autocomplete);
    }


    /**
     * @SWG\Put(
     *     path="/judges/{id}/bookmark",
     *     summary="Додати суддю в закладки",
     *     description="Додати суддю, в закладки поточного користувача",
     *     operationId="judges-addBookmark",
     *     produces={"application/json"},
     *     tags={"Судді"},
     *     security={
     *     {"passport": {}},
     *      },
     *     @SWG\Parameter(
     *      ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *      ref="#/parameters/X-Requested-With",
     *     ),
     *
     *    @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Id судді, якого потрібно додати в закладки поточного користувача",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     maximum=15000,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Response(
     *         response=201,
     *         description="Закладка упішно створена",
     *         examples={"application/json":
     *              {
     *                  "message": "Закладка успішно створена"
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=401,
     *         description="Необхідна аутентифікація користувача",
     *         examples={"application/json":
     *              {
     *                  "message": "Unauthenticated",
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується PUT.",
     *     ),
     *
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, неіснуючий id, або дана закладка вже існує",
     *         examples={"application/json":
     *              {
     *                  "message": "Неіснуючий id",
     *              }
     *          }
     *     ),
     * )
     */
    public function addJudgeBookmark($id) {
        $id = intval($id);

        if (UserBookmarkJudge::checkBookmark(Auth::user()->id, $id)) {
            return response()->json([
                'message' => 'Закладка вже існує'
            ], 422);
        }
        UserBookmarkJudge::createBookmark(Auth::user()->id, $id);
        return response()->json([
            'message' => 'Закладка успішно створена'
        ], 201);
    }



    /**
     * @SWG\Delete(
     *     path="/judges/{id}/bookmark",
     *     summary="Видалити суддю з закладок",
     *     description="Видалити суддю, з закладок поточного користувача",
     *     operationId="judges-delBookmark",
     *     produces={"application/json"},
     *     tags={"Судді"},
     *     security={
     *     {"passport": {}},
     *      },
     *     @SWG\Parameter(
     *      ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *      ref="#/parameters/X-Requested-With",
     *     ),
     *
     *    @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Id судді, якого потрібно видалити з закладок поточного користувача",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     maximum=15000,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Response(
     *         response=204,
     *         description="Закладка успішно видалена"
     *     ),
     *
     *     @SWG\Response(
     *         response=401,
     *         description="Необхідна аутентифікація користувача",
     *         examples={"application/json":
     *              {
     *                  "message": "Unauthenticated",
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується DELETE.",
     *     ),
     *
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, неіснуючий id, або даної закладки не існує",
     *         examples={"application/json":
     *              {
     *                  "message": "Закладки не існує",
     *              }
     *          }
     *     ),
     * )
     */
    public function delJudgeBookmark($id) {
        $id = intval($id);

        if (!UserBookmarkJudge::checkBookmark(Auth::user()->id, $id)) {
            return response()->json([
                'message' => 'Закладки не існує'
            ], 422);
        }
        UserBookmarkJudge::deleteBookmark(Auth::user()->id, $id);
        return response()->json([], 204);
    }



    /**
     * @SWG\Put(
     *     path="/judges/{id}/update-status",
     *     summary="Оновити статус судді",
     *     description="Встановии новий статус для судді, наприклад: суддя був на роботі, і пішов у відпустку",
     *     operationId="judges-updateStatus",
     *     produces={"application/json"},
     *     tags={"Судді"},
     *     security={
     *     {"passport": {}},
     *      },
     *     @SWG\Parameter(
     *      ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *      ref="#/parameters/X-Requested-With",
     *     ),
     *
     *    @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Id судді, для якого потрібно встановити новий статус",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     maximum=15000,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Parameter(
     *     name="Дані нового статусу",
     *     in="body",
     *     required=true,
     *     description="Існує 5 статусів, які свідчать про перебування судді на робочому місці:
    1 - На роботі
    2 - На лікарняному
    3 - У відпустці
    4 - Відсутній на робочому місці з інших причин
    5 - Припинено повноваження
     *  Щоб встановити новий статус, потрібно передати id нового статусу, і якщо відома, то дату дії статусу, тобто до якого часу даний статус буде діяти
     *  Дата дії може бути передана для статусів 2-4.  Для статусів 1,5 дата дії не враховується",
     *     @SWG\Schema(
     *          type="object",
     *          required={"set_status"},
     *          @SWG\Property(property="set_status",  type="integer", example="3", description="id статусу"),
     *          @SWG\Property(property="due_date", type="date", example="2018-09-21", description="Дата дії статусу, у форматі Y-m-d,
     * мінімальне значення >= поточний день
     * максимальне значення <= поточний день + 1 місяць")
     *       )
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="Статус судді був успішно оновлений",
     *         examples={"application/json":
     *              {
     *                  "message": "Статус успішно оновлено"
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=401,
     *         description="Необхідна аутентифікація користувача",
     *         examples={"application/json":
     *              {
     *                  "message": "Unauthenticated",
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується PUT.",
     *     ),
     *
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, неіснуючий id, некоректний формат даних",
     *         examples={"application/json":
     *              {
     *                  "message": "The given data was invalid.",
     *                  "errors": {
     *                      "due_date": {
     *                      "due date не валідна дата.",
     *                      "due date не відповідає формату Y-m-d.",
     *                      "due date повинна бути дата, що пізніша або рівна today."
     *                      }
     *                  }
     *              }
     *          }
     *     ),
     * )
     */
    public function updateJudgeStatus(Request $request, $id) {
        // валідація вхідних даних
        $request->validate([
            'set_status' => 'required|integer|between:1,5',
            'due_date' => 'date|date_format:Y-m-d|after_or_equal:today|before_or_equal:+1 month|nullable'
        ]);

        $new_status = intval($request->set_status);
        // якщо due_date не передана, або передано статуси 1,5 due_date=null
        $due_date = $request->due_date ?? NULL;
        if ($new_status == 1 || $new_status == 5) {
            $due_date = NULL;
        }
        $old = Judge::find($id);

        $last_change_date = new DateTime($old->updated_status);

        if ($old->status != $new_status && $last_change_date < Carbon::now(self::TIMEZONE)->subMinutes(15)) {

          // Додати в чергу відправку повідомлень всім хто:
          // підписаний на судові засідання даного судді
          SendNotification3::dispatch($id, $old->status, $new_status)->delay(now()->addMinute());
          // відстежує даного суддю
          SendNotification1::dispatch($id, $old->status, $new_status)->delay(now()->addMinute());
        }
		
        // оновлення статусу судді
        Judge::setNewStatus($id, $new_status, $due_date);

        return response()->json([
            'message' => 'Статус успішно оновлено'
        ], 200);
    }




    /**
     * @SWG\Put(
     *     path="/judges/{id}/like",
     *     summary="Поставити лайк судді",
     *     description="Поставити лайк судді від імені поточного користувача",
     *     operationId="judges-addLike",
     *     produces={"application/json"},
     *     tags={"Судді"},
     *     security={
     *     {"passport": {}},
     *      },
     *     @SWG\Parameter(
     *      ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *      ref="#/parameters/X-Requested-With",
     *     ),
     *
     *    @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Id судді, якому поставити лайк",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     maximum=15000,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="Лайк упішно поставлений",
     *         examples={"application/json":
     *              {
     *                  "message": "ОК"
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=401,
     *         description="Необхідна аутентифікація користувача",
     *         examples={"application/json":
     *              {
     *                  "message": "Unauthenticated",
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується PUT.",
     *     ),
     *
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, неіснуючий id, або користувач вже ставив лайк для цього судді",
     *         examples={"application/json":
     *              {
     *                  "message": "Даний користувач вже ставив лайк для цього судді",
     *              }
     *          }
     *     ),
     * )
     */
    public function putLike($id) {
        // перевіряємо чи користувач вже ставив лайк
        $is_liked = UsersLikesJudge::isLikedJudge($id);

        // якщо ставив - то 422, в іншому випадку ставимо
        if ($is_liked) {
            return response()->json([
                'message' => 'Даний користувач вже ставив лайк для цього судді'
            ], 422);
        }
        UsersLikesJudge::putLike($id);
        return response()->json([
            'message' => 'ОК'
        ], 200);
    }

    /**
     * @SWG\Delete(
     *     path="/judges/{id}/like",
     *     summary="Видалити лайк судді",
     *     description="Видалити раніше поставлений від імені поточного користувача лайк судді",
     *     operationId="judges-delLike",
     *     produces={"application/json"},
     *     tags={"Судді"},
     *     security={
     *     {"passport": {}},
     *      },
     *     @SWG\Parameter(
     *      ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *      ref="#/parameters/X-Requested-With",
     *     ),
     *
     *    @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Id судді, для якого видалити лайк",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     maximum=15000,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Response(
     *         response=204,
     *         description="Лайк упішно видалений"
     *     ),
     *
     *     @SWG\Response(
     *         response=401,
     *         description="Необхідна аутентифікація користувача",
     *         examples={"application/json":
     *              {
     *                  "message": "Unauthenticated",
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується DELETE.",
     *     ),
     *
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, неіснуючий id, або користувач не ставив лайк для цього судді",
     *         examples={"application/json":
     *              {
     *                  "message": "Даний користувач не ставив лайк для цього судді",
     *              }
     *          }
     *     ),
     * )
     */
    public function deleteLike($id) {
        // перевіряємо чи користувач ставив лайк
        $is_liked = UsersLikesJudge::isLikedJudge($id);

        // якщо користувач не ставив лайк - 422
        if (!$is_liked) {
            return response()->json([
                'message' => 'Даний користувач не ставив лайк для цього судді'
            ], 422);
        }
        UsersLikesJudge::deleteLike($id);
        return response()->json([], 204);
    }


    /**
     * @SWG\Put(
     *     path="/judges/{id}/unlike",
     *     summary="Поставити дизлайк судді",
     *     description="Поставити дизлайк судді від імені поточного користувача",
     *     operationId="judges-addUnlike",
     *     produces={"application/json"},
     *     tags={"Судді"},
     *     security={
     *     {"passport": {}},
     *      },
     *     @SWG\Parameter(
     *      ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *      ref="#/parameters/X-Requested-With",
     *     ),
     *
     *    @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Id судді, якому поставити дизлайк",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     maximum=15000,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="Дизлайк упішно поставлений",
     *         examples={"application/json":
     *              {
     *                  "message": "ОК"
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=401,
     *         description="Необхідна аутентифікація користувача",
     *         examples={"application/json":
     *              {
     *                  "message": "Unauthenticated",
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується PUT.",
     *     ),
     *
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, неіснуючий id, або користувач вже ставив дизлайк для цього судді",
     *         examples={"application/json":
     *              {
     *                  "message": "Даний користувач вже ставив дизлайк для цього судді",
     *              }
     *          }
     *     ),
     * )
     */
    public function putUnlike($id) {
        // перевіряємо чи користувач вже ставив дизлайк
        $is_unliked = UsersUnlikesJudge::isUnlikedJudge($id);

        // якщо ставив - то 422, в іншому випадку ставимо
        if ($is_unliked) {
            return response()->json([
                'message' => 'Даний користувач вже ставив дизлайк для цього судді'
            ], 422);
        }
        UsersUnlikesJudge::putUnlike($id);
        return response()->json([
            'message' => 'ОК'
        ], 200);
    }


    /**
     * @SWG\Delete(
     *     path="/judges/{id}/unlike",
     *     summary="Видалити дизлайк судді",
     *     description="Видалити раніше поставлений від імені поточного користувача дизлайк судді",
     *     operationId="judges-delUnlike",
     *     produces={"application/json"},
     *     tags={"Судді"},
     *     security={
     *     {"passport": {}},
     *      },
     *     @SWG\Parameter(
     *      ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *      ref="#/parameters/X-Requested-With",
     *     ),
     *
     *    @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Id судді, для якого видалити дизлайк",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     maximum=15000,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Response(
     *         response=204,
     *         description="Дизайк упішно видалений"
     *     ),
     *
     *     @SWG\Response(
     *         response=401,
     *         description="Необхідна аутентифікація користувача",
     *         examples={"application/json":
     *              {
     *                  "message": "Unauthenticated",
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується DELETE.",
     *     ),
     *
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, неіснуючий id, або користувач не ставив дизлайк для цього судді",
     *         examples={"application/json":
     *              {
     *                  "message": "Даний користувач не ставив дизлайк для цього судді",
     *              }
     *          }
     *     ),
     * )
     */
    public function deleteUnlike($id) {
        // перевіряємо чи користувач вже ставив дизлайк
        $is_unliked = UsersUnlikesJudge::isUnlikedJudge($id);

        // якщо користувач не ставив дизлайк - 422
        if (!$is_unliked) {
            return response()->json([
                'message' => 'Даний користувач не ставив дизлайк для цього судді'
            ], 422);
        } else {
            UsersUnlikesJudge::deleteUnlike($id);
            return response()->json([], 204);
        }
    }



    /**
     * @SWG\Post(
     *     path="/judges/photo",
     *     summary="Додати фото судді",
     *     description="Додати фото судді",
     *     operationId="judges-addPhoto",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Судді"},
     *     security={
     *     {"passport": {}},
     *      },
     *     @SWG\Parameter(
     *      ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *      ref="#/parameters/X-Requested-With",
     *     ),
     *
     *     @SWG\Parameter(
     *     name="Дані користувача",
     *     in="body",
     *     description="id судді та фото в форматі base64",
     *     @SWG\Schema(
     *          type="object",
     *            required={"judge_id", "photo"},
     *          @SWG\Property(property="judge_id", type="int", example="1", description="id судді"),
     *          @SWG\Property(property="photo", type="string", example="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoHBwYIDAoMDAsKCwsNDhIQDQ4RDgsLEBYQERMUFRUVDA8XGBYUGBIUFRT/2wBDAQMEBAUEBQkFBQkUDQsNFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBT/wAARCAHgAc0DASIAAhEBAxEB/8QAHQAAAgEFAQEAAAAAAAAAAAAAAAIBAwQFBgcICf/EAEoQAAEDAgUBBgMFBAgDCAEFAAEAAgMEEQUGEiExQQcTIlFhcQhCgRQyUpGhCSOxwRUzQ2JygqLRJJLwFiU0RFNjsuHSJlR0k8L/xAAcAQEAAQUBAQAAAAAAAAAAAAAAAgEEBQYHAwj/xAA7EQACAQMCBAIIBAUEAgMAAAAAAQIDBBEFIQYSMUFRYRMiMnGBkaGxFMHR8AcVI0LhFiQzUjRDYoLx/9oADAMBAAIRAxEAPwD6poQhACROhACEIQAl5KZCAEIQgBCEIAQo6KUAIQhAIodwmcUpF0BKEKPmQEoIshCAEIQgIdwpQhACj5UwAsocNtggBCh3ClACEKPJASodwpQgBQ7hHyqUAIUaUN4QEpblSOSpQAhCEAIQhACj5lKRAN8qlQ3hSgBCh3ClACEIQAo8lKj5UBHRMkTO4QEoQhAOhCgi6AVOl0pkBFgpUWClACFHTdHAQEoQlJugGQkU6kBPRQTdQTdCAY8JdRUalKAEIQgBQ3hShACEIQAhRqUoAQhCAA6yNXohBFkBBF1KEICHcKU1giwQCccodwqiiwQFJOmOw4S21ICPlUqPmUoCPmRqUoQEfKh3ClCAh3ClRypQAo+ZSo0oBU6FDhZAB6qUaboIsUAIUhtlFvEUAKNSYCyHC6AX5UqfTsjR6IAQpAsj6IBkIUHhASkTqOAgJQkToAQouEuv1QBtfkoJ3Cg7lSgAlCEIAQhCAEIQgBCEICHcKUIQAhCEAIQhACEIQAi90IQAhCEA17AIBulJugOtsgHUE2S6vRBN0BJcLcqL2QhACEIQAhCEAKHcI+VSgBCEIAQhQ3hASocbqUIAB3smsCUnCnX6oB0g+8pBugCxQDIQhACiwUoQEE2QOEEXRYICUIQgEU/KmSEoCSbo1JSLqUBBPkpUaUcICUIQgBCUm6jV7oBibI1JeUKuATcouVCEwCblQeUIVQCFBNuiNSqBhyi5UJHSNYCXODQNySbBR2K4Klyi9ly3NPxA5awGpko6GR2NVjCWuZRkFgd5a+Cfa61x/aJ2n5jIlwbL1BhlGbaH4o4tLh52O/6LX6uuWVKo6UZc0l1STePf2Rdxtaso8zWF57HddSNTVoGWMzZigp3HMhwx8gGww3Xz66tljM4jEs1PjbSZnrsv0umzo8Pij1u93uBI+iuZanbKGebfw7kfw884wdRMg9dlIOrzXAR2aysk72HPmaI57WMjqoOv9CLLe8sYlieAYdNBXYy/Hntb+5lqIWxyD0cW7H8lbUdWp1G1UXKven9ic7WUV6u50O7VHeAdSufYpi1XjdJaHF6nDNY3dSsZqb5i7gVqsGVHl9Sa3NWOYgJT4Q+drO7/AOUC6jc6vCks0UpfFL7lYWspe1sdr7xvn9EXC4tR5Tjw573QYtVSF4I1VD3Oc31BvythjqMapjeir45mB2prJpHXt5Em6xEOI6qf9S3f/wBWn+Z6uyx0kjpIKZahgec5ZphTYrSPw6c7NkcP3b/Z3C2wOB4IIW12t3Tu4c9P5PZr3osJ05U3iQ6jUoJui5V6eZOpSo1KL73QDIS3ai5QE6lKW+3KCboBkIUX3QEoQhACFDeFKAEIQgBANihCAYG6lUwbKWnfcoB0KLhSgBRcKVA4QEqOqlKTYIAJsErjdGpSgBCFH1QEA3TJLgoVcAm+90E3UIVcAEISk7qoGQo1BQXAhATfdGpI7lBN1XAGJvwo1OUareqo1dSKamfJpc8gbNbyT5bqLaissqk2VZJmxMLnkNaBck7ALUcR7S8Ppqk09MDVyA2Ja7S2/kD1+is8YbNjhaa+qfFTg3+x0zvD7Od1+mys44aGgdrpqOKN9ra9N3D6laDq2rXXsWslCP8A2e7+C/UylC2i95rPkTU9qGKzVEcWDZZmxXxaZJjO2CGLzu92x+l1R7SYZM9YRFl4YpV4G2dgnrpKEBznxA6TC2Q/d1E82vYHzR9tLGljbNad7NFgPotfzQW2pcW7ydkmGOdIRC63eRkWexw6i1j6FossDHiOVCi6dWpzyfVtJfJIvHZpvMVgusr5Oy5kalZBg2GRQuYLfaJf3krvUuKy0+Iuebl1z7rF/ahIwOa8Oa4AgjqCqTqgW5WlXPEChmNPEV5bGSjQXV7l6+qJvuqZqDYbqwdU77lJLUWjjN/MLWauuTll5LlUkjImpPmmbVEubvsSsR9qPqmhqSZox0v/ACXhHXZ5S5g6SMdkCrqjlKgNY8uqnmd7/wD+55H6ELYftWxBPCwEZZQyUsEJPdtbIP1v/NXIq/Cble9XW5KXqyKKksGXFUSBvyq0dcWnYlYdlQPDv0umZUC3KuaOvTg0+Yo6SfY2amxpzBpfZ7Dy124P0Wbp86U2GUYM7DoDmsGnhtzb/ZaE2p3G6sMcxPu46OlbZ09ZUshja7ffdznfRrXH8lt+n8XTpySTyWVWyhNdDsTsyMdfu+71eTyQsdVZwr6aUNOFfaY3HZ9PNq29RZaYytINwSrmPEHi3iI9ltv+p/xMcRm4vyx+ZZfgVHtk2CPtWwwvLZIKiIg2ILQSD7crYqDMVFiVu4l8dgdEgLDb6rQ3OpqyaGaogjknidqZKRuD0Pr9VZjB6lmuZ1Sap7tw9lxYk9QeitZ8Qarav0kUq0M9EsNL5/kQdnTlt7L+Z1xp1C6nqub5Ez/9travCq+a09PMYopX7ax0B9V0VrwW6rLoOlarb6tQVai/Jrun4MxVahOjLlkOhLq42QHLOFuMhQDcqVTAJBumSIVMAkGyn5UqNQva+6oBncKVFvVSgBCjzUoAUN4UoQAw9E6pg23TaggGUXClCAXUoO6g7+iPlQEoUaVBNkBNwlJ5SqfmUsAPmUoSanKoHUE2KgOKgm6YAxKUi6jryguspJAOCgnZQhACN/ooJsrOuxCCkYGPla2WQEMYT4nWFzb2C8p1I01zSZNLOyEnxeKOR0bD3j2/e8m+nv6LX8Txd9Q62o6fJW9ZXgN0ss1vosRUVBd1XMNa4i5E6dNmctrTHrSKk9UXA78Kzkm8zsVSfOGuDr7HYq2kl3dHfcbt9Vxy+1mc29zNwpJFaScAAnjqqMkrZJO6cNTJAWkHe4KsHzPeCLqLu0h1zqbwtPqajOTyme+Ei1waN9FhjKU80z3wj/CD4f8ASR+SuHPc7qq00Y72SVos2Vgkt6jYpNNvrurOvVnKWWyvuKenV1Ug6o2tPQlVNNgDa6Uts923qrbnZUXTsVFiCCOVX03PCkMt0UMsGPlYPtjCfla4/mpDSYWHzKrSgCeS43DUrGkxQ+tl6c7ApuHm3kpAIan0eN55CfR4VTnkCj4hwVbiITY7HUSD93QQODD5yyWB/Jrf9SvdO4tyrapPkLb6nettgru3rum3LIL+OpFgL78q4ZPfqsL4mBo6gXKqsqHMAJ6lX9HUakX1I4TM7HNa26vaerc03DrLBR1PS+6u4qi9t9lttjrU4NeseM6SZTzPkqHN/wC/gr58IxVjNMNXDZzAb7a4zs4fUH1Wa7Jc3Y82aXK2caaOnxymaX0lbA7VT4jAPnjJ3Dm7amncXBVvT1Ja4G6ykfc4jHHHM5zHRvEkUsZs+J44c0/9X4K6rw/qFtGt6RJJy6td/f8AqYi7ozlDHZHQ9SkOuuZZd7Vn02czk3NMLKLGZAZcOq4v/D4jD0cz8Lxw5nQ8bFdK1fVdZpVYVlmDzg1+UHHqipqU/MVT1IDjde5HBWJQCqdygOubKmCJVUW39UoNlId5qmAMgFCFTAHUN4SoB3VMAdQ3hDeFKoAQhCAa4RcJVIF0AulBNlKQkoCSdtioQhSAqngoJUKoBImuEpOykCXcouLpdSlABO/KEJC78k6AkkDla5jGc6ajqhQUTf6QxJ3EETtm+rndAtMx7O9VmzHq7L+CyiFkA0vqWvtq/Edt7A7LJ4ThlNgNH3NO0d44DvZiPFIfX09FoV3xD6Sc6VrtGOzk/HwS7mUp2jSTn1fb9TKU9dXNhc6vqGz1DzcsjH7tg6ADr7rFYtV/v4al7S/ug5uoC5YHWuR+SqST+qtZZbm17X4K0TUtem4OmpPBlaVslvgoTT3PNwRcEcEKwnqg2+6pVtV3NVHBo0skadJHyuHI9jyPqrJxc43K5DqOoynNpMysI4Q8lQ5xsNgl1Oc3c7t6phHdOGW3WtSqSk8s9RSwOIcALFAZvayqsjIcWHg7tVVsephPzN5XkCnC0OiMThctNx7FUms0sAPLTa/or3uy17H9HbKlPDpe9p6j9VVvKBQLNntHTdGi7gfNqrNF3sP4hZQxn7tp6scWlUAmng+YUadlUaPCz3IUkbH3QFrNGP3h/u2SsZZkQ9OFczMvqPoo0eJnoEBbltgVJabqrp2RpQFMjQ0uPQK1dH3hjadtXid7BXro9bHDokEPje48nb6KuQWz49ZPqqbW+Jzj9xnHurxzLA2VN0Vw2MccuKomC1ALW6vmfx6BXEVRpNvJSY7uJ6DhI5lt16xqyjugZGGovYrIU9V3bmi41E7A9VrhmFMwyPdpa0Xv5IZWVP2uGnjYRXTxmR73C7KWIcF3qTwOpv0C2vS9QqwmuXseVRLBm825apM7VmXi6VsFdgmJRYjFVtF3xBv3mf527EeW/kun02Jid+4AaeHNP8VzigLaSFoZc3NyXG5cTyT6rL0mIGMgBy7po/EkeVQmzB17PO6N+B81N91gMPx0EtZKTvtq5ss214cAQQfULp9td0ruHNTZhKlOVN4kVbkKQd0mr1U89VenmVASFOpU726qdvVVwRwVGlNqVMH8k31UcFBncqUhN0zeAqAkcp0iL2UcAdCUGyZUAIQhAK4m9lCEKQI0qVGpQTuqggm6VS42UKQAm6EJCb8IB0JN/NRcnqgJcVoHbRmHG8IyTW02WKYVeZK5opqGMvDAHucGl5J4DQSSfILG9r/bvhPZZA6mEZxLG3M1soozYNB4Lz0v0AuSsPkmXM+c8Fw3Meb4afCMZc8y0tJQNP7qmdwyXUTdzudrW2WqapqlJQnbUZrnXXyXf44L+jQltKS2MhkLJcWQcvspXziuxWUB9ZW2t3sltw2/DR0Cy80tyeqmplcD4hZ48uCrKSax5sOQSuKapqVOivRUlhLojY6VNv1pbsaSays56lu7CdjwfIqnPVaj4evKtrarg7rm13fyqNpMvYxwJVxGui0nw1Ee4d524KVjRKwSWs7h7fIq5bGX2I2c3gqXR+IzNbe+0jB/FYOTc3k9F4FMRW6JhGA4XGx2VwyME6b3BF2nzCYRXaWnleLyngFuyBzmOH9pFuPUKuxoD45P7OTY+hVQXjEc1r6DpePMK5bRtMktID4Jm97A718lc06TqdP3++h4yqKJRipXPbPTW/eN8TfVU6yMPjhmA+82zvQhXjZHd1BWAfvYXd3MPRFbCGGaNv3HHvGfVe06PLTb/fkeMZtyMMWWZYbaTdVdAvO3zAeFJbuQeosmhI1xknlpYVYIu2W4Hgv5Pup03v7ptJAc31RZyoVEeLah5qdO/s1M9t+iHcGyAolosFFvRVdN+iA08IAjiBcAfujxFLp8Jd0PCr2/d2H3nm3sFJi72ZkY+60XJ8lXrsC0MJaBtueEhjsTt7q+ADhJOdmjwsCoviIDWfMdynQpnJaFmyR7OpGwV4IC94a3jqfJW1Q3vHaYxsdm+qp03KmPZGa2sD3C8EB1AHhz/M+g/irqacF5ZfSCdczxy8jgK6loPs1IB8o5CxjoyHAGxedyOjQrinVlTTS7lHuZOnxDh7xpe7ZjfIeayUUwaLA/Vas9zu8Lhvba6vKOv7sBrvzWas9SlTkk2RaTNrgqTtutjwnFdNmPN2FaVBOD1WSpqrSQbrrmh69KjNPJjri3U1hnQ2uBAI3unBusJgmIiUGNx8Vri6zGpd4tLmF3SVSHc1epB05crKmr0Ug+SQGykGyvzzH1bJtSp6vZNqsqYIFQG6kCyQOUgqgKgN1KQG6kFRwBgUwN+iQG6m9lEDoS3amUQIlcg8JF6IDpFINlCqAJUXsEakt73QASShChyAlYTOWZ6fJeV8VxyrF6bD6Z9Q8eYaL2+qza5J8TuB4lnDs0GV8Ma/XjuIUtBUSMv+7pnSAzOJHHgad/VeFxJwpSkuyPSKTkkzh/YTlir7Zsfq+0PMQ72gkqXy0rHAkTuB2IB+VtgB52Xpaol5t+XRW2E4NQZXwajwjCqdlJh9FE2CCFg2a1osFE0l1wK+rRtFNReZSeW/Fmz006mG+i6FtVuDmddtx5hYeWV8hIPCv6uUgbc+asXC5XI9RuXVqZMnBYWCkGeJVWx3TRx3KuooN9wsDvJnp0Fhg1WV63DHzN1Ri8jRu3i/ormjoy4gHbyKzcNKABYWcOCs3Z2DrbssK1xy7I1uloGVkLomnu52+Jl9rkc+3qEhgc5pJbpkYbPathqcMtIKiFv7xpu5g+b1HqtV7Vs64T2a5Qqs04l3r4YGH/AIanbqlqHW2Y1vmf0WcegV7mKjRhmfl3Xj8O5axuuV7vYvIwyI948juXAtk8h6rl+e/iS7P+z/8ApLDq3HY8QxzBoTW/0bhY+0VJiFr7A26g88Lw/wBq/wAQPaf8Q+OnA8Ip6ygyxiAMUWC4K1xnIPD5nt3cQRuNmjyK3bsr+A7P2N1OXsw5oxOlytiGFXglbp+1VFbS2sGvaCGg2JG5Ox42W8WXCVjpdJVdbrqLf9q6/qWlS6nVlikvibzmT9o1hFPiWAy4DlGWoy/mWBwjxPEasRiGoBLTG+JoNrHTfxcFcLzB+0w7UWOkpWYFlzDqijlfC8dxLNcA7feeF6zwD4BOyfAMGqMHraTEswUE1UcQijxCsIZBKee7bGG6R0tc8BZ2p+DrsZqQ7vez7CnvePHIe8LifPVquveprHCVo/RxtnPzfv8ANkI0rqe/Ng8Q4N+087R6KqY7E8vZdxOmvd7GRy073DyDg5wB9bFfQ7s4znB2j5Cy/mqmpZqGDF6OOtZTTkF8WofdJHNt9+q4niH7OzsWq8bhr2YTilJCxwc7DqfEniml34cCC63oHBeisPwymwjDqWhoqeOlo6WNsMEEI0sjY0Wa0DoAAtI4ov8AQrylT/lVFwn/AHbYWPDGWZS0p3EG/TSyhy3clRv0VQhBGy54ZQpEXCjTuntvdMgKViixVQD6KQEBqnaZ2m4B2PZNrc15nqX0uE0ZYxzoozI973HS1jWjkkrzpH+0w7OXmKGny5mmaorJO7iDYINThewNu82vdehe2Hsqwntr7PMWyfjUksNFXtaRUQW7yCRjg5kjb7EhwGx5FwvKGTf2Y7MHxmoxLFe0HvBA10dEKHDAHtJFg863kXF72A5XSOG6XDc7actWk1UT2SzjGF0wuvXqYi6lcqaVJbHSov2iHY9LijqCrnxnDBSEsklnoC+MPB38THOvvtx0XTslfET2a9oTDLgec8KqpnnSyGSYQyf8r7FecsT/AGV2W5qNlLh/aBjMNXI7vJZKqhhlDieLhpbbqeeq0Gs/Zt5synjD8apcSw3OOF4U0y01BEHU09VM3djHB122LtzZ3A9VsMtH4R1CXLbXMoS884+q/MtVcXdJetHJ9F5YDT07IgbTVADnO/C3or/DMEDh3xbYuGmJp6D8RXzb7Dp+37s8zu+uzXiuK4Vh/fOfPhWLt72nqbm+hgNw1vkWkWHC+hHZj2yYP2iN+ykDD8ZY3xUj3ffA5MZ6j9QrO64JrWSdeElUpY2cfuyX41zjhLDMvjNI1hDW+IN/UrXZqQtuBy7dzlu9dTNlaZXgRxM2a3q4+Z/2WFrKJzWGRzdLeQDz9Vzm9tnTm3FbGSoVVJYZq0sWkhoCoFtjsstUUxaBfk8lWMsVrrE5LoakrXQ2Dt2+XksxBVB7QWm611+xNjurrDpSyZm/J3WUtLudKSWSuzN2w2cwkOvd569Atuw+sFTHubuC0OkmNhvss7hta6KRpBvvuPNfQnDGtcnLTk9mYW8t+dZXU2u5Tg+qoRyCRocDcFVAbLtMWpLKNba5Rx+qkHokBumBspgqXspBukB4CkcgoQHBsnBukBupBsoAcOUtSg3U6vRUwCWpxwlUWvuo4Au/mo90HgJXG6mCblQTZBNkp3VUA5QhCkAQo0qVAkjEZqzNh+T8CrMXxOcU9FSsL3vO59APMngBcy7I+1LFO13LeJ4/V0DMMwiTEJIcKg3710EYDTJIeLudqsBsABytW+L3MQiwvBcAA1Cqn76SNp3cb6Ix+bifouh5My9DlTJuFYTCwMbTQNaQ0W8RFz+q5zq+s1PxVSzprEYrd+b7GXoW0fRxqS6t/QyMx3O91ZTOte/CuZncrHVTy1pXD9VucN7mepxLOZ+txsUjW3Km1yqsbN1z6pJylll4PDHc7rI0tOdjp1s6gchUKaEk+Eb+SzlHTaAHM8D+rT1V9Z23pZllXq8qwirRwiNoLPHEfzCyMbRp8wqMMNnam+Fx5b0Ku42k7jZ3VpXSNPs+iwYOpPLyUa2qhw6kmqqiQQwwsL3SONgAOVxJ+RMY7aMXlxXGe/wrLkgMTcNmHjLAdnsHyk8kldwrcOpsTg7mqibPFqDjFILtuDcXHXdXJbYD0C6Fa1VZU3KmvXffw9xaSXN1NJyd2YZZ7PKM02X8HpsOY43fIxl5JD5uedyVn307d9t1kJBcqg9t1zjUqcq1WVSby34l5B4WDDz0xbwNgsfLHYrPztBB2WIqWBpK0e7o8u6MlRnkxzwqJb6K4kFiqTwsFNGSiyg5t+l0tvPZVSLKm5q8j0E0+ymwUqLb3VMAAApt+SFPAVQM0WKqNaDYHgdEgHBVVg3upIoxwHAP07SP21HoFfUVAKp8Udv+GhF7fjd5q2iabrYMOjDIQR1KzlhR9JPD6GNuJci2ExXAcPx+ifSYjRQ1lK8WMczA4Lz52j/DjW4DUHHskzz97Ae9bSa/3sVt7xO6+xXpS2ylw1NI811HTtSr2O1OW3dPo/gYeUVLqcj7G+1F3aBgkrMcgFHmLD3d3UUJ2c7oJNPmeo6Fb1PTl572qBL3f1dO3p7rCY12X4XLnejzlh9OykzTTsNO6raLNnp3HxxSDgg2BDuQQFs8mt7y2CzpDs6Z3DfQLB6vToV6npaMcKXVeD/M96UmtjWcSpDG+zrd4d9I6BYWohIJvstqqqdjpHR04Mz7/vJTxf1P8lhqymDHlv3nDkLnNxRdOTfYzlKfMtzASR77KnGdL/8ArlX80ViVZPZYqxy0z26GxUE/eMa5ZimktbdathExDiwn1WxUrrroWiXb9XB5VI5Rt+EVXeMLCd+QsmDcLWMOlMUrStljOoL6d0O7d1bLm6o1O7p8k9u4/X0TA3SaVIK2RFkVAbJgdgqYNim8lUoyoCpBukaUyER01wkbwpUAN8xTbJNSkFUwBTyoJsoJs1QTdSwAUNUoUgCj5kealAACjdSovbZQJnm/4gsu1GKduvZoww97SYg8xauQ0wkyv/0/zXYKp4FwAQPZWuKYpl7NGbHUsbX12L5WeKh0kbTop5Jo3M7vVwXGNxJaOBpvyFZVtVCZnBmJRxv6se4WH5rknEdOFpOpVpbym1n4LBm7SbqRUZdEV53eSxdQ7U6wQ6unjbrLG1cP/q0zg79FaisiqXEsfe30I9wdwuCapUk5YksGepLYqtFzYHdXEDNwTt69FQZq5I1DzasnQxOlF2WlA5b1Wvwi5ywTnLljll7RQEW1tJZ+NvRZuFo0i/iH4lZ0MTB/VOLHdY3rJRssdvA7yPBW/aXZ7IwFapllRrLjcXHmqzW7DV9HBQxtrfK49OhVVoA6Wv0XSbW2UEWEnkCDb181Te8DZVDsCucZ4zY7A+0TJdE+QspquSZkm9gS5ult/rb81fOlKtJwj1w38kRRc9qOdTk3C6AxaTWYhXQ0UId01O8Tvo0H9FthOi4XBviqrX4dU5Mqt+5hq3yG/GoaD/C66H2ndoEeS+zavzHGQ+QU4NOOQ6R4sz9T+iw15YOpQt3TWZTbXxzhHtGWG0+xa452o0NPniiyfhcZxXHJR3tTHE793RQjcvld0Pk3k3C2OscDf0XCfhhwqHLuRMf7QsxVIjqMUlkmlrag7tp2E3df1dqP0C4xNnbOvxm9p1ZgGCV1ZlXsxw1wNdNSPMU9Qy+zXPG+p9tmj7o5WvX+hQqVakIS5adJetJ9M98L37JFzSq8uHjd9EeyYquCtD3U88U4Y4scYnh+lw5BtwfRBFrrH5ZythWSsCpcHwWiiw/DaRgjigiFgPUnkk8knclZBx2XKriMIzfJvHtkztNtrcov5VNwI+qd5ukd6rHlwIpaghSgI1JjuErVIKAqAj6qqzY36eaot5stM7XMnY9mzKkn/ZPHKjL+aaMmbD6qJ/7t77f1crTs5juDfjkK7tKMa9aNOcuVN9X0Xv8AI8qknGOUsm8VmMUOCUZrMQq4aKka4NdPO8MY0k2FydhuQN/NbVh7w+BpBu08EcFeSvh5+Ien7eaPHezftAwyLCs6U8MtJXUDm2irYxdsjmA8OaeW+xFxxkfhP7QcVyrmrMHZBmWrkqqnA6mSHDKiclzzCPE1hJ5BYWub5AkLo1loVeg6sJbThh47Si+6fcwlasp4a6M9KUOYBLmzEcGkAD4YI6mL+8xxIP5Efqs7rA281yd+KEfEbHSsN2nBrPH+a4VbtJzk/Ce0bIGD07yJKquL5mtPMZYWAH6uJ+i2N2EpVKcYf3R5vkt/sWuUdQmaHMOo2b1Kx80Lqhlifs9O3k8Fw/kskN+d/RUpIQ9wdJ4mjhvRYOtSUl+/qTi8Mxnduni7qnb3FOOZCLF3ssTVwxNJipmGTT96Tpf3Wfqma2Xnf3MI+UcuVlJBLUM0sYKWlHzO+87/AGWuXlvzrCW/76Lsi7pVOV5NPrINJIJuVjJWW6LZKuOIFzYQZN/vdPzWEqoy24PK0ypFxbRmU+ZZLWnkMUzXDkLZ6STU0Hz3Wrnwnff2WawmXUywG4WW0ut6KqkHusGz0j+FtFK/XTsPpZalSP3W04a69KPQr6f4Sr86cfI1u/jhZLtMlUXculIww6YdEmpSCpAqA3Tg3KpA3Tg+SEBwd7qdQulBUqmAMNinuEnKYEAcqIKaEIUwCEIQEA3UoSk32UWVRPyrS+1nO8+ScoVdRh1Oa3GpwIKCmb80zjpaT6Am59lugGy4nVPkzx21T1LZXNw3LLBFov4ZZnDfbja/PSy17Wrydpb/ANL2pNJfHq/gi7t6aqS9botzP9n+UjkXKVNh81Q6sxGRzqmvq3/enqXnVI4/U2HoAsjV6ZAQ5rXD+8AVdVEtysfO/wA1xbWtQcm1nobBRppIx76WKnFoWNiBN7MaGj9FbOoI6p13E6xw48/mrmaS5P8ANUAQx33XNP5hcjvavpZtsykVhYQxw+emGq5cz8YVzD38DWyvie0ciVnH6cK5wypcHhjXgtOxa4XC2Gmp44jqa3u9XIb90/RXFjZU7l5UmmWNetKG0kY6ixcOA7xvfsHD2ffHuOv0WfpKhlS3VE9srPIchY2sw/D7d49zaZ/42O03Pt1WPGIUkDiRM90oNhNTtIJ9wdiupaTpOpRkuSnzx8V+/uYyUVV3gmbbHa3hNx1aeQnBsP5LTznGeCp7uaiMkNvDVMeNz6t6JJ851LLhsUVuh1ErptHQr+pFNU8e94IRs60uiNxcb3XEfiZweoOX8Mx2kv3uGVAL3N5YHEaXewcB+a2+TPVc07Rw/kf91rmc8dqc2ZfqsIqHCmp6kBsr6c+It6je/Kurbh7UaVxGpyrCe+/buXK0u4nsl9Tm/bb2j5dz/wBk+FvdXRszAyRkwowCXarFsgPkLG4J8guQZ57Zp809k2EZTnhkbU4fUBz6ouGmWNoIYLc3F/0W8Yt2J0ErSYMUqYz01xtd/stCx/sPxNocaTEKWoHQSNdG7+YW10dAp0IRjyuSi215NlJ6Pf7uMc+4sO1zt4w+u7CMu5JwAVEUsMQbiYkZoDg0XDWm/iDnEn6br0/8PPZtTdl3ZLgmGxNYa6pibXV07P7WeQaib9QAQ0ejV4Mz/wBnOZMFpJnyYbLLG0G8kBEg/RX3w4/GFi3Y7jdPl3N09RW5QmeIyai5mw4k/fZfcs829BuPJc74s4erVdPdKw7Nykn1ln9PA8KdGvbVM3FNrtlrY+k8gVB3BS0tbBiVJDV0szKmmnY2WKaJ2pr2OFw4HqCDdS87L5XrJwk4S6mfh02KLjcJXcqSbKHG6tD0Fub+iHKVG6ANSL8FRY35UtFuUJlRu9lWb5qgHeJcO+KD4pcK+HzL0VNTMjxPOGIMJocOc7wxN476W24YDwOXHbzKyenWFxqVxG3to80n+9/I8KslCOWcG+OTLlR2Pds+Te1vLdqWrq5NFXHGba6iKxDiPKSMlp/wqtj/AG25cxr4nMBzrluuZNSVlLQyV5YCO5maNMjHeZDLXt5LyZmXN2de2PH5MVx2pr8brpXXa6QERxj8LG/daPQLbchdm+YI62GV8UVIAQ4GWUXH0F19aaVosbW2owvJqVSMXFtd0+3njsYenpuoXs27ehJxbz0Z9E+yfNMOce2XM+bZ3imw6GleY3SkDTECGtJ/ytufdW2R6qftc7f2ZgLHDDsNBmiDvlY0FsY9yTqWi5K7JK/FMHpqo4xTsZNFpf3TXuuPI8XXeOzHDKbs5weenih+2Vc8muWpHg1DoLb2sri70WpTpzq2sOaTioxXgu7JS066g+WcMNM6/a3KkHp1WkSdovdX1UEm3k8JI+1Gl/taWdg4s0BxK59/pfVor/hf0PT+X3OMqBt07WtkuGGeXoOjVY1YY1//ABk2t3SGLhYir7RsIhpDNU1MlBCLXvGS93ta6uqTFqWsbeglhia4X72Z15Df+6f5rVr/AEa8t8qrRkl7nj/PxZ5qhVp7zi0U6+KWWIOc1lDTj7rSfEf9lr1VEy57tpI/E7qtiqqUMY6VxD3dairfZo9mrBS1dK5xLO+xGT8TR3cf58lc3vrdqe+3vL6hPbHUxUsRa1znFsbBuXPNgpwvEgaowwQ1FTtcyhmmJvu48+wukq3BxPeiO9792BcD0TU1TqlaXlzgOGjYLHUJU6U0+rL1mz0vfSW1vbGPKP8A3K2LC6nuS1oJDb3K1mlluAfRZekl4su18Nag6M4tMxt1TU1g27Vxbr5KFQw+bvadvm3Yq5PC+hKNRVYKa7mqSXK2iQbqdKppw7bhXBQa9k7OiRSDdCLKodwmVJpuUwN0KDjlqe9kiFRgEIQqggn1UXKEIAuUDlCFAkilW1Io6OonO4iic/8AIErkfZFh09LlAYhWAivxSZ9XMSLHxEkD8iutVkAq6OpgPEsbmH6ghanT0xoqCmgLO6EUbWaOdNhwtD4kjP0lOpj1Yp/N4X2MnZvZx8cFKdysZnbHdXczr+isKh1guCarW3ZsVNFnLc8DUqIuNmyGN3k4bKuSNQNrey5x2zdr8HZbhMQZAKvFawO+zQv/AKsW5c8+QvxyVqNvbVr+vGhRWZSMlb29W6qxo0VmUuiN0xrN+HZRpDV4vLA2EDw92+0j/RreSVzKq+K3DKtwpQK/KsRNhPilI4hw9Hi7QvM0+cRm/H34hmmPEK+pfsKrDqh0T4R5MYNg0eQW/YDVvbGxmCdo74YzxQZooGTMPoXWaf1X0bwrwda2OK9xidTwfRfB4ybpPhqFlFSuE5T+i+jz8j0ZljHW5ghbVU2JU2Lxu376nqBJ/NbpTT+HS9pB9V59y1heN0VY2slyZlzFT/8Av8t1f2WVw8yw2B/NdXwPGX90wS02I0JI3irAH6f8wXdaCi4cqjy47JYRq91QWXy9Ph+X6I2mrebWDrDyWLll0k9CnkrO9FweeqspnlxsDcq8gtjxpU8bMpz1Om+6xtRUarhXM2p4NxYBYydx1L2SMtRgihUv1NK1+tkILrbLNTuu0gG6wOIcHdejWI5RnbaO5omeqoR4XOb2uOV5yzTQ0mIAtqqWGpb5SsBuu79plUIsLfY8rgOLVJllfd+y53rFTNbC7G/W9ClO15ZxTT8TqHY98UVX2Q4HQ5YqcLGK4DSkiBzZiJ6ZhN9DSbhzRc2G1uF6VyF8S+Q+0F8UNNi7cOr3/wDk8RHcvJ8gT4T9CvnrUSmXUbtLWHq3Ym/UhYKpIOq9h5BvF7rkmq8JWGoylVScZvdtePmjUL3QbSq26S5X5dPl+h9dtQdYgixFwR1Ch3C8FfCL2tZnj7UMHyvUY1U1eXq1kzPslS7vGxvbGXMLC7du44BsfJe8tVyFwzW9HqaNcegnJSysprwOe3llOyq+im89ybuQXkBKjkrXiwHBLvfotJzz20ZN7OWOGOY7TQVAH/hInd7Of8jdx9VyP41e0DGMn5XwKhwbE6nDHYjUSiodSSaHvja0eHUNwLnoQvC75HTPdK97nSOddzn3JPW5K6boHCMdUoRuq88QfZddvM2XTdI/GQVWpLEfLqew83/HcXOfDlLLgIJ0trcWfYe4iab/AJuXmXMWP1me84YlmTHRFW4xV2dJMWWDQBZrWj5WgbABYSGz4zbkkFXdKGmU8m42/wCvouv6botnpW9rDDezfd/E36y0qzt2nGCb8Xu/qZiiJjqIy24AsQAt4y7VgSsLjYW91o2HOsHj0s0rZMJeWTRm/B2Cz8Xhm7WqjHZHrnsFxk1NFNhzzrcPEz09F1yNjoyvN/YPiLqXNdK0us2Q6Tv5r1DWwtjlO211v1lU56McnHOJKSttRkl0kslnNh4qo9QFneSxE+DvY8/KfNbNBI1jQVYV9T3jwAbWWUpyecGtUqtRS5V0MFFg5a/WXAH8TtyravdSQNOuXXK3caG6339FmZw0MN2Of7cLE1EkrbiGCOP6k/wVxKKn7SL2Lc36xobMzZ5gnqY8q4A6tdJLrNZmytPctP8AciF3Aeg0hZmk7fMuSY3FgGYsVpMJx02jdHBJ/wAI6T8LZSB16OWq9pmbsDoaF9Hi+fWZdDriWCifH9okH4Q3xOH0C8UdpE+WDic4wKnxfFKcHw1OItMes+fit/Bch4r4X07UItqCjN91hP5Lr8TNWulW96pufqyxthP6s+mlUGNJ0i3kfNUYzpcDwvCHYT8WuLdn08ODZrkkr8sW0Rvd+8qKIdNJO72f3Tx08l7jy/mLDsy4RR4rhNVFX0FXGJYamM3a4H/rhfL+saBdaNVxV3g+kl0f6M1e8sqlnPln07M27DptcYtfbqsxTSXstew+Uu+8/UfQcLNUziLLOaNXw44ZiqiyjacEm8bmeYWYuFreFSaKhhB3Jsti1+q+nNCrOraJPsandw5amSb7qQASlJsFLeVshZlRA2SA2TNVURY7eE44VMGyZnRMFCo1SkZymJsqACVCgndSgIsFKFFwgJQhCiySEl1dzIW/e0mx9bLVqwm9z5BbYNtlpeLVrYsRqGPHdsBGh+sEP89ulvVabxMsWvMZCyfr4LSY3VhM7xWKuJahjr2eCrFzw9xN/wAl8w6pUfO4s2qmiV5d+MA/995cBv8A+Hl/+YXqAm68yfGLCWVuWpgLgxTMv9WlX3CMsatT9z+xt/DTS1Slnz+zOKZFFScei+yTQwzh3hM5s1eicMjqZoAK2npah55EfiafzXnfIdLJVYjqdg0mKxXuWMNl6ByLRxz4pQ0UmAVuHU80rY3ESMIaCfRy+s9MrRtqU6s84Sb79lnwwdM16pGHrPsvLP3z9DacJwulZIGw4W6OV3ApRYn6BdHwLJeLTsDh31PF+Gpfc/kt6wLLmH4HTNZR0wi23dy53ueSs5GLDbZaVecd1q8nCzgox8Xuzg97rU6rxSjhee5qdNkBoANRVFzvJjbBXLciUDd3PmcfMu/+ls2nySvNgtcq8Q6jUeXWfw2+xgXeV5P22eNu0LtYxrL2csbw6kNP9lo6t8MYfFc6Wm25WqT9vmOsI10tE8dbxkX/AFVv27wuw7tRzPC8aS+rMo9nAOH8VyWtrDrILum5XbbG4nO2pz5m8pfYxz1K6pywps66fiImZf7Rg8L78mOUhU5fiEy5UNtVxVNA627i0SN/TdcFxXEO7jsHW/n7rnmbcwfZonMa8F3X0V47mqljmMnb8RX1u0+ZP3o9PZpq588ZXfjWBU9TimDRyOidW08D3RtkbbU0m2xAI/NcLxWo3cLEW5v0Xov9nb8UWVMvZcxDs7zVilJglVJXvrcMqq2QRw1AkDQ+IvOwcHNuLncO9F2H4rsx9heC5Sq6rHmYLiOZathjw+nwiVhrJZnbNce7OzQSCXO2stcu7Z15OpzbnRNO49c+WhcUfLMX+T/U+e009m2sdxvZYeaQygNuS3oAshUMd9wvDiBe97XWIfIWuAA26la1I6TOXNubx2PZvpci9pGXseru8NHQVHeTthbd+jSQbDryvZY+N7s2dcu/pZtvOjH/AOS+fMziQHMuSOT1VuZRckgkdStT1Th2z1epGpcZyljZ4MFf2FC8kqlTOVtsz6JN+Njs1duZsVaB50J/3Ut+NbswP/nsRHvQPXz1ge4yAX0kgc72UzB2uxBuD12WC/0NpmcZfz/wWC4ftnHmTfzPQPxa9tGXu1yvy4cuVM89NQxS98ZoXREOcRbY87BcGje55F3OcOSCfS11aiaxsOvoqzb6Cb+h/wBluun2VLTreNtRzyx8TM2lvC1pqlDoi+p3nruOiyVJJ++Zb2Cw9M9rDvvb9Vpees0YjRYqaSlqnU8Bia491sST68rMUaTrT5IldR1SlpNu7mqm0mlheZ152IUuHxNlqqiGkYTf968N3AUx9puA08rGxTS1juvcRnTf3Nl54ZM+dzXve6QnclxufzKz+Eu0SNHB/gs3T06C9t5OeXH8Q7vpa01Fee7Pov8ACHlXFu1N8mYaWmOHYNQzdyKqqNzNIBctY0c2uLn1XpjtJpsbyfgrsUp4afEqWDecEua6Mfit1Hn5Llv7O7PmD4t2Jx5ZinjixnCaqd81MSA98cjy9sgHUb2J9F3rtczFRZeyFi/2uRve1dO+lp4CfFJI8ECw9L3J6WWXp/0Y8sHg0e+1++1Gsq1eWX7jjWUO0qszDmXDcNmpYIIquYROewklt78Ls0uR2uN2zi/q1ebeyynMvaDl+Pkipa648mtJP8F696LStc1e8tK8Y0Z428jyje13vzGh12RauRhEdUxrQOGix/VaLmjs8dVxuhq6Kqrouoe93du97EAj3XdXAKNIIN1jbbi2+pPFbEl5rH1L2hqlei89Tx1j3ZticTZKfL+A5fwOFws6okaWE+pEQaT9XLyd2vZSZguKztqMy0NdP88eCUzY2MPkS25v7lfVjF8pYfimpz4GiQ8utsfccFeNPiyyM/AiySmFBSU4Bafs8AbI887rPR1a21anJQTjUW7XbHk0dL4Z1aF5dKjJYcl0PAlfQsikcY6eV39+Uk397r2X8AOJ1U+T820UtQ90NLXQuigLiWxB8btRb5XI3t5LyXmGnd3zw58j7nk7L1R8ADO6wvPLbf29If8ATIuecXxUtHq57Y+6Mlr9LkpzWD2LhUnjDTb3WxUxuRZaph7iJh6raKUm7SuLaNP1kjmk+hnKBx7yM/3gtpAutXw4XlZbfxBbRfbZfU3DX/itmq3vtg7ZSOClJ81K3Ex4KoOOFTB9U4JVUUYw5TpEwN1IiVFA4SgklCgB1A4R1UE25QDKD1Re5UONkAyEocFNwosmK8fu3b2NjutPrpD3jiDa5W4O3DvZabV7PcPIlaRxS8WywZKx9tmLnu695P0WNfZryNZP0WTlI+qx01xJxa6+ZdT3llm00+gB264H8XtGH5ZwOr0kiOqfESOmpu38F3kXvyuX/ErhLsU7J8QkaPFRyx1H0Bsf0Kpw9WVvqdGb8cfPYz2j1VRv6M3/ANl9djx/lWSlZXNFUzEXAG1qE+M+24XeMj1FNS4nRzw/9oomxyMktVNcWbEHe9151w6rfQ4jFI2qfTlrrh8YuR6r0VkSt/pDDGzjFaquDhY99GGEFfXGmRVeEqL75Ox63T56eezWD2rRytnpopGkkOaHD2V0wrV+z7ERiWVKCQuLntjDH35uNltDXbLgdWjK1uZ0pdU2vkz5fr03SqSpvs2ipq3SPPKlI43VKtTY8Ejxz8ZGWH4Zm2hx1jbQYhB3T3f+4z/6I/JeXK+sDA+53PC+k3bd2cM7UMgV+ExlseItHfUUruGzN4B9DwfdfMLNv2jAqmrpK6J9LV073RSwyCzmOGxC7TwnqkLuzVCT9eG2PLszGXNNxlzeJrmZMabTxyuB48+VybG8VdPI973fe2Dbq+zVjzq2qLQ4Fg6BajU1Gp2/t6LdZyLRCzTNkYbg7m1rbH3T4KQ7G6IMAGqdgtx1WPdK4EknrwshlSPvsyUDbE6ZNR9gFaVZYg2ZPT4Od1Sgu8l90dglLpXXvsd73VB7GhjhsHDzO91VbpMd7lW8/UA79fVacfUuMRKETLknY+hPKpuhDmbfeFhYDY+/6Lauz7ImKdouaqPL2EshOI1jXmP7Q8sYA1pcSTY9Auvz/BJ2j3s0YM5v/wDOIv8A6Fg7rVbKzqejr1FFvfDZjq9xbUcQrVFF9cM87U8tnuLtiT8vmq872l+kEu8yeq7oPgi7TYtWmLBySb6hiA48vupnfBT2mED/AIbCiR1FeN/9Ktv5/pef/Ij8y3hqVpy49JH5nBg28gHJ6Kp3gFgPrZbx2pdimaex+OgfmKCmi+3Fwg+zVAlBLbF17Dblc/Y67+dyeqzVvcUrqmqtGSlF90e9OrCquem8p+BkIWanbHbyXPO0aP8A/ULbbAwsO/1XQYLuO29hcrQ+0iFzMXpHud96C1/OxKzFg8VkjWOLo82lSfg19zX6M22+iz2FkMewm5A5C12meGO3vcbb9Qs1hcri7m1vJbSjgTO/9g2J1FFmqkmpJ5qWVp8MkLyxw+o3XqWqxisxeobPXVc9XLwH1EheQPLfheTew2ZpzHTA8G54Xp+mfqLCb38l51PaLqmtjsvw9YQ/Ec8SV5aTDQU7nX6a3+ED8tS9Mjblc97E8muylk+N9Swtrq0ieUO5aCPC0+w/iuhBy43rd3G5u5OL2Wy+BexWETq62THcKkXb2ChzitfdVR6ksZJfwV51+IjCBmHAcZDGa5IPG0htzsN136tqRTU0srjZrGlxN+gC8a9uHbXiGTqeq7vBq6plqmucyogdGY23PBBO/tZbvwkqdSpXrz2jFY+LNz4Yt60rxVaXWODxhm/DHx1r7i1j1XpH4DKcNwPO0w8QNbTRg+0bj/NeUs151rcYrJ5pKippS9xJbJC3n6L2p8GGAHA+x1mI1AlZU4xVPqXOI2exvgYQOgsCtf4zqwp6VODftNJfPP5HRuJq9OVJpdW0ehKON7pWlrC4XW0Up8IB8PoStZw8RulDmTvBtazogR/FZT+hKSvsKlon876mj9CuNaRTXMmnuculsjcsEljNXE0vbcnYX5Wzg26LTMuYdS4XJEymgbC0O4G5/MrcvmX1Nwy3+Dw/E1W9/wCQHG6DwpJuhbiY8E7OSkTqqBINyUzdylbwpHKkQGbymdylHPKZ3KiwQTZF7qN7bqVQEE2QTypUO4QEoSnlMqMmF9rLT8Ub3NTIw8grcFq2YKcMrXvDd3C+olajxJRdW0yuxf2UuWpgwUm+6saoHVeyvnjlWVSBp4c4+QXzRqdJrOTa6b2KAdusVm/BG5lytiuFOF/tdLJAL/iLTp/WyybQ7pGG+rip7wsNzOxpG/hF1qdOpKhVjUj1Tz8i5jJwkpR6o+a5papteIe7tO15Y4PsLEGx/Vd2yRisuB0UMeM5hwWnjY0BkTJGmRvu6/8AJc9+IDJ8OV+1bFYRR19RTVkn2yBgcQwtfuQOOHalmcj4VX09DDUUGSsvYWwDevxqpDnn1tz+q+wuH7uNxCFeH9yT7/kd0uLpXVjSqro1nt+Z7A7Bs3UOLw1eH01e2s0kTNMY2twd/e35rsjfQLyZ2Z5vr8JxqlqanMmGVEcRtLRYLQktc07EF9z/ANBeqKCrjr6OKojcXMkaHNcfVabxZYStr13Sj6s98+ffuzgev2ro3TqRXqy+/fsi7d0VM7JidlRllDRyuaXFVI1xLIsrg1eXviw+FuHtkoJcYy1LFhubYmWLZPDDWtHyvPyu8nfQr0dV1wBIB3WKmnJ3usNR1yvpldVrWWJL6+TL2NqqscTR8Ou0HJuYOzvHZcMzNg9Vg9axxboqWFrXerXcOHqCtLqHWJvsPM9V92szZXwTOFC6jxzCaLGKR2xgrqdszfoHA2XKqv4POxWpqe/f2dYP3l7nSJGt/wCUPA/RdRtP4oW7p/7ujJS8sNP59CxnpVRP1HsfH7BsLxHMmIRYbhFBUYpXynSynpIjK9xPHhC9AYp8MuK9imTMLzFnCoFJmTFpTHR4JE4F1PEBd8kzvxbtAaOL7novp5lHs8yr2fwfZ8s5dwzAoztahpmxk+7gLn6leAvi17S4+0PtbrIaOcS4TgzP6PpnMN2vcDeV4933A9GhXNjxjX4ivFQtafJSjvJvdvwXgsm0cPaT/vIVJvPLuclp7vja0kNHF1TftJYcJaeYBjiR1uBZOy7fFe7iDstxlLC2O4R9ZI9F/A/gZxDtZr8TLf3WG4a+x6B8jmtH6al7pLuF5t+CDKJwfs7xLHJIyyXF6qzHHkxRiw/NxcvR2q6+aOLLpXWqTx0jhfI5VrddV72eP7dvkMBfqocbFKTsoB3Wm4MEeb/juwJ1b2Y4VijG3dh+IBrj5NkaR/EBeEInaXcWdxsvqJ27ZRdnrsizRhETO8qX0jp6dtuZY/G0fXTb6r5c6t9VtvIr6A4EuVV0+VHO8ZP5Pc3zQ6qlQcH/AGv7l7G+zwQtmy72J1Pbm3EcNwaqbFmagpHVmH0stgytaCBLFqP3XWILTxsQVqbJLAcWW/djmepOz/tFwLHg7THTVAEwHWJ3hePyJ/JdBuatahRnUt/aSyveu3xMxqNsr2yqUH3R56xvAcRyxjFTheLUU+G4lSuLJqWqjLHsPsf48KtQGzhb9Dyvsr2gdjWQe2zDYHZny9RY1G+MOgrLFk7GkXBZKwhw/Oy46/8AZudlMtS58FfmajiJv3DK9jgPQF0ZP6rA2P8AEewnDlvISjNdcLKz5bpnAaumVYSxHdHjjsVmEeYaUX3va/nfovot2Hdi81XU0uYMwU7oaaMh9LRyizpHdHvHQDkDqs32SfC52bdktRDUYJgImr2cV2JSuqZh6gu2b9AF3KMWAXjfcaQv4unZRcU9m31+HgRjbuksTKoFlJulDtQUrTvS8zJ4C9r7pHOHXZM48/xVCaXQNpWg/wB/heVSRJGndpuOsw/AZKUTQ9/Unu2xySBpcOtvp/FfOT4h8bFbmOoYIKmJkA7sGOY6bjk2Xqj4kszurDUCvyzR4tg0LCynr2TxmRryPEQx9uu2x3svn1nPEIH1UxhfW0lyfDK5xH6khdc0y2lYaXClJYlN5fj5L5Ha+FLT8LayrzW7NSqYJMTrYqaB8zpp5GxMYRcuc42A/Mr6nZKy63JuTcEwKJ12UFHHAb/iDRq/W68DfCjkx2fO2vC3yvEuH4M12Jz3b95zLCNp93lv5L6Iyl7iS5oufwrjXH17z1KVnF+zu/e9l9DCa9X56qprtuX2Fi8t7fVbTSG5C1jCQNW+3utlpfvALUdHWJI1OfQzlA494zputuvfdanhUffTxsvyVtYFhZfUXDMWrZvsare+3ghNpUoW5GPBA3KENNypYKMqA7KUqYcKpEZvCnfqlbymVGCCbKCfVDtylcqMEhxU3uN+UvzKVQBf1Rc+qhqlCZIOxWGzHAHU7ZerTZZhWeKtDsPmJ4aNR26DlY+9pKtbzg+6PSlLlmmaVJsPVWs2pwP7wM9uVeztsdtwdwfMK1eNJsLL5r1q0dOTRt9GeUjGFjC43D5D5uOyrRNLfusa1E7SH7myhhA4BcfILmVaPLJovThPxa5JOLZXo8xQOl77DXd3P3I3MLzz9HW/NcgyBlmOrp4aiHJ7Kt7baq/G6ovZfzDDf+C9lY0yjrsOqKHEXQspaqJ0MkbyNT2uFiAF4Vzrh+FdkGaqmgzI7EcyiJxko6WUubTvjJ8Djw0nzFjuF3P+H2twVN2dd7x3WfB/p5HQtAvvT2srGXtReY+afVdV3PSWWnf8O0MqKaw2MeGRgNB8rromWc8MyhG7+k5/s+Ek6nTVUw/dHz36ei8Vu7c87YlQ/wDdNPhWR8Db4W1dVZht5RsO7j6Naue4/jkua6l3/eOLZsq2DU+srZDFTReoYTZo9XEey7Rql5Z39o7aUeZP4b+K7l1W0CV83TrPCfxa/JH1bps34ZiVDDV0FZFXU0rdUc1O8PY4ehGys6nGTPexsF8qMr9oGYOz2qfJl7MklJI83fT0R1UxP95p8Lve31XY8D+OfNGDQsjxvL1DiwFr1FM8wOPu3cL5s1XhrUnN/h5KUfDozW7ng65tG5UfXXyfyPdElWXFUDOSV5Fg/aB4VpHfZQrQevd1bCP1CrP/AGgOXWNucq4oSf8A341qUuGdXzvRf0MZ/KLyP/rZ6vMlyqT3G59Nz6LxnmX9oLN3JZgWUWxTEf1uIVOoD/K0fzXBu0D4lc/9o7HwYlj0tJhzr3ocOH2eI+jtO7vqVlLLgrUbiS9PiEfN5fyRcUtGuqj9dcq8z0X8UfxXU9DT1mTslVzZa2Rroq/Fqd12wjh0UThy48Fw46brxYJA51ybA9TuqU1Q6qe+WRxklebucRYk+qgOe6M2J0Djyuu2aRpFvpFBUaK977tm52dpTsockVv3fiX0L9IIt/e3NisvgGD1mZ8cw7B6CMyVtfOynhYLnxOP/RPssBHI5rr2va24Xsj4Lex2SIHP+K05ic9jocKjeLHSdnzD33aD7rx1vUYaXaTrze/ZeL7F3d30LS2lN9e3vPT+Tss02S8q4VgVJYQUFO2AEfMQPE76m5WXBt1SF1lLR1uvl2rUlWqOpPq3lnJJSc25S6sa+6kOtskLrKNW/K8WQKjTY359D1XzA+Ifs7d2ZdrGM4YyMsw+aQ1dF5GKQ3AH+E3H0X091WC4R8XPY1J2m5D/AKVwuAy5gwRrpoo2DxVEHMkY8z8wHpbqt54Q1VadfqFR4hU2fk+zMxpd1+HrYl0lsz57tkItuLDkq8gfd22w/isffUCB+RV5TSCwBHC+j8qSOlUnk9i/Cb8SrMLipsl5qq9NKCI8MxCZ20YPEMh8vwuPnbyXtGCUHe+3ovj7TPDdhY3G48wu89lHxOZzyHSwUL6pmNYVCNLKWvu58begbIPFb3uuXa/wi7uq7mywpPrHon5o13UtAlXk61rjL6r9D6PUcmlzeq2CN3huvLuS/jByxjTY2YlRVmEzm1yG99Hf3G/6LtGE9reW8QpI5YcQvG4bF0bh/ELWbLRtToycJUJZ8ln7HPb7Sb2g/XpSXwN/G3spB3WpM7SsCLbitDh6Md/sqcvafhIb+4dJPJ0AYWg/Ura7fRNTqtKNCXyZh/wVw9vRv5G3l1hyR6rnvaLnCanpnUGFRw19SfDO0yaCweQ9SsRj/aNW1jSxrnYUw8Shuu/14C0+UGd5lfBFUg79/A7xH1Pqun6DwXUhUjcah0XSPXfzM9p+lSU1Vr9u36nCe2R1Fg+GTupaLE8tVDmkSNZO/uHH/C0uj/NoXj7MtXLLM7TO2oafmLQL/kvXPxGvlgo2SNhL4j4W1Eli5p/DtY/xXnnsn7LZ+13tLocJ7k/0dE8VOJVDP7OnB8Qv+J33R7+iyGv3FO0lOc3iMFk6/CUbXT1Nvbqz038G3ZwcodmsmN1tMxtdj8gnDgPE2BtxGL+R3d9V3sBpIDXEBRFBDRU8NNSNbT08LBHFA0WaxoFg0ewCrQtc9w1M1DzC+PdRvZ6neVLmXd/Tt9DklxWderKo+5mMLicGb7jzWZp77ELHUcTQwAc+Sy9O29ltWj23M0iyqPCNhy20un1HSQG/UFbGsNl+FwYX28NufVZi5C+o9FoqjaQjg1G5lzVGxybI5SXvsmHHKzxajKGcqUDZTKMdMkHCccIRJHKYcnhKOVOpAKTuhCEAKPlU8KNSgA+ZH3kt3IuEJIbe6CSEoJRf6qL3KmqYzRupJ5pJZLxPOpj3kAf4fcfwWCnraSFri+rijA3JLgLD3K0XtGzBiMfxQ5Ipw+RuD0uH1NM+G/gklqGgh9uCR3bQPcreayna55c5oc4dXAFcV4ipW8nONDrF4f3NitXNJOffoa9i2aadsxp8PglxWqAB7untYernnwtHqSsI+oxzFCWSVJgB5p8MGzR5OmeOf8IC2eoYGteGNaLm5AFgT5lYKubc6JZn6OkcfhC4deJUpPCM2t+paR4dDQg9/WiBztnMpCZJnejpDv8AksfmnJWF52y1UYPNQCjppvE2rfZ1RG8cPaTwb/zWVp/CD3EIjb+NydsjDLa76mQ/K37oWEhc1KFRVKbxJbrBcU5ypTU4PDXQ8BdrvZDjvZfjkj8RY7EcMkfalxMXMco/CfwuH4T9Fz3EDNWwtic9zoB4hE11mA+dh/Er6g4vhNDjmGzYfjMMNVQzjS+icwPD/f1XmntM+DiSXv8AEMl1DIonXd/RVdJY+0cnT2d+a65o3GFKslSvXyz8ez9/gdT0vialVh6C82l/27P3+B5Fp6gQyd1IWRMH3Y4z4vcrIwSB7eLjqFXzl2c4/kSuLcXwaow43sTNGQD7O4I9QsXSzBwG4I9CulU69OvFTpNNPwN2t68KnsvK8tyK7Cg5pfD76QsLUXZcEHbkHlbewjUbAD0usfieEiraXxlrZAOD1V1CaWzPK7tFOLnS6+BrAeL+NpLfIGypPcC4HgKpO10cj2uBbvweVSkaGhpvYnqTsrtM1WpldRTJ6/zurkP0t09B0uqmXMFqc05hoMAw8wy4lWy6IIHzMiLneV3EAL2n2N/BNQZckp8UzzURYxiDLObhdMSaaM/33GxkPpsPdYTVdZtdHpekuJbvou7MRV1Ghb5cpZfgupyj4bfhmre0yvp8fx+nlpMpxO1APBa+vI+VvXR5u+gXvuniioqaKnp4mQQRMEccUbbNY0CwAHQAIiZHTwshiY2KONoYyNjbNa0cADgAeSm4C+d9b1ytrNfnqbRXSPh/k0a9vql5U5pbLsv33GuU2v1VIu6/ooBPUrXDGlUmyNSp6vZBeFEFQmyqA2tblUGuuBdOHlE8boHi74q/hmmweqq865TozLh0rjLiNBA27qdx5lY0csJ5A454XliIgW2X15PiaQ4AtOxB4IXmftr+DPD82TT4xkp8OEYpJd8mHS+Gmndzdh/sz+nsuy8NcXRjGNnfvpspfkzb9M1dU0qdd7dn+p4vgt4TyPVZrDqkMIGo+qxmMYRV5YxypwnEGNhxGjkMU8DZGv0O8rgkJ6KezvHqDb3cWjcBdkjJSSkujOhWteE0pQeUdFy7UEVMbb2u4fd6r1HlKsbBhUDHuAIaOV42ocYkp3tc3Z4A3B3Hks6e0bEWs0mrlcBtYuWasb1WcnLGcmVrU43NJRbwewqzNmHYO1n2usipxIdLC91g4+S0/He37Dst1fcNpZqp4O8TiGF482k7FcDyrmbFcdbVxYfHR5mAberyxiDi2aePq+nJ5cPIG/oVQqsLZmbC6l2UZZcepKa5q8o4v4MVw/zMDuZGjyG/os7PVK9WnzUdjWp0rWjVdOruvHsepso9vGVM6tdR4VjcWD4yeMPxdpa1zvIXNiPYrGZh7VaPLGIVFJi2H/0JiUZBNTSTl9JKPMkC8d/N7dP95eG657KhrnwOfX07DZ9NUDu6ymI/+VvMLs3ZFlftG7RMPipKGhGZsts2vjpfTvpR/wC3NbX/AMmoeYWLr8SqhTzdSUMd+3yMZcWVG2bqxn6vm/3ky3aVjx7SMXo6LAoayqxiteI2UYcHMmJ4cxzSWEebgdhuV6Z7FeyCl7HsoCkcY58bq7S4hVsFw5/RjT+FvA89yrfsi7GMqdlUL6iiM9Xj05cZ6udl5IAf7Nuwu3+9a56ros0hewuDmzM/FGf4jovmnjHimWr1XQt3/Tzu/H/BrOq6t+KhGhS2hH6/4KEh1HcXCvcNh1SagS23mrFl3nbf25WcoIdMQsLErndrT55o1ZGRp2nm1vZZOljJIAGo9B5q1gjvbqtgwGh76pDjuxm5XauHdNlWnFYMfc1VCLbNhooPs9LHHwQN/dXCjcqF9CUoKnFRXY1RvLyT8ylChq9kUJabJ0idSIEjYp2pAB9U7UAw5Qdigcot9UBCEIQEEpTwmP6KFAAlbygm6UG1kKoZvKZU3G6bUqMkcm7d8o1VV/QWa8Kp3VGIYLVxunhjbd0tMXeOw6lt7+11m68DW63B3C6AuZwZlw7NFTiz8Nk1/wBH182H1MbhZ0U0ZGppHqCCPQhaBxDpsFCdxTW8sZ+HcytpWbahLouhZVUdweiwdfCSQWR63f3uFsNSNzssRWxawdT9LfIcr5z1Sg1JmzQeUYV742P/AH8xlceIolXaJ3M300EH4Ru9ypNcY3ObTRNitsZHC7iobo1eJ7ppPVadUWGeyL2kewH90yx6yP3JV21rQ7W673epWPDnBwu4Nv8AK0K/iOqO3G31VlPZk0NXUNPi1KYKyniq4CLGKdge0/QrmuPfDh2cY/I59Rlmmp5HG5fRudCf9JsulteQolFgCAru2vbm3f8ASqOPubRc0ritQeaU3H3M4VVfBtkOoeTDNi1J6Mqg4D/marKT4LMpk+HG8YYPK8R//wArv3eWsLpg/qs3DiLU49K7MjHW9QgsKtI8+t+B/IL5my1dZjNa7q01DYwf+Vq3LLfwz9muVXNfSZWpqiVu4lrnOndfz8Rt+i6gXEjY39LKk517ryra5qVZcs68mvfj7FjWv7qs26k2/ieS/i3+C6DtKDs3ZEhhwzN1NGO9w+ECKKva37pbawZKOh4dYXsd1xTsU+O7NvZFWDKPadhtZjNFRv8As7ppBoxKittYh39a0eRsfIlfRtz9lybtu+GfI3b1R6sfoXUmNMbpgxuhsypj8g64tI3+676ELcdJ4mtq9utN16HpKS9mX90fj1x9feazXtqnO61F4l9zbuzrtayl2r4UyvypjtLi0ZF3QsdpnjPUPjPiafotuvuvlv2gfBb2rdiOLOxjK09RjtDTnXHieASOjqWgdXxA6gfPTcK7yJ+0G7UshSNoMyQ0WaYYD3ckGLQugrWW2/rG2N/8TSr+44Fp30fxGh3EasfBvDXl/wDuDyjfOm+WvHDPp5rsgv8ANePMrftL8hYm1jcey/jWBTfMYdFVGPYjS79F0TDPjj7FsSjDv+1/2Qn5aqklYR/pWn3HCmtWzxO2l8Fn7ZL6N1Ql0kjv2q4si5XFZfjI7GYI+8dnuiItfwRyE/8AxWsY1+0D7G8JDu4xivxV44bR4e83+rrBW1PhvV6z5YW0vk19yTuaMes0ekwdk7QSQACT5BeFM3/tQMPp45GZUyNUVUnDKjGKsRsv593Hc/6l5+zf8YHbR201f9D0eJz0kdT4GYVlmnMRdfoS28jh7uW02X8PtVuPXucUo923+Sz9cFpU1GlDaO7PpF2rfEf2e9jVK92Y8wQCtaPDhlERPVPPkGNO3u4gLxZ2hfGd2mfEXjYyd2ZYLU4HRVZ7sRUbu9r6hvF5JRYRN89NvVxWM7Hf2e2dc+VLMTz7WOyphkpD3xvPf4hN6Bp2ZfzeT7Fe9uyjsayf2K4L/RuUsIjoWuAE9XIddTUHzkkO59hYDoFl5vh3hZf0n+JuF0b9mL+33fuPFfiLvr6sfqci+Gj4KsG7K8NlxfOYp8zZsrmESslGumo2u5Yy/wDWPPWQ+wHU7fmr4PuzzMcj5qWnq8Bmd1w+b93/AMjgR+S7Pr3UmVaHX4k1OvcyuvTNN+GyS8EumEbBaValnFRoya+J5UxH4DC1xOGZ2GnoytoDcfVr/wCSxEvwI5lBs3NmEEX5MEoXsRk2/Kqd5fqsjDjHV4LDqJ+9IzcdcvorHpPov0PH9D8COLRzMlrM60dK+M6oauhpJe9gcOCLuFwumO+FLDMQhoJ845hqq7GqZ4NNmrBacUdQQPllILtfuRddxdIRuCD6HgqIamSAv+zafF/WUsu7JB6L3jxnrGHH0uE/BL9CzuNUvK+HKe68kvyNEj7Dck4ZiUGIYrgdPX4m1jWxZie0SulI4dILAF3mbXW0VH/BtYyRrGsaLRVFMLMt02HCuHStZr+w/umu/rcPnN2/5SsUX2Lvsvg/HRynb6LWL7ULi/nzV6jk/N5Mc6lSe9STfvCrm76xnAlI+7Mw2d+atHSnVcuc89JozpkHv0P1UOkDiREDE/5oX/yVNg7ySwBY78Kx8YsjkvqWulpX95WMbPTggfaIGnWP8cf8wSt0owyVjXMIe0i4c3qFgMOpg1rejubrLw4YS9ssE0lLLfcx2LXe7Tt9RYrdNLs+dp43PCckjYKKmdNI1rRcuNgt1oaRtHTNjb97lxHmsFk6GWOlvWSR1FbveWKLu2kX/Dc2P1WyL6b4f0+Frbqp3Zqt3Wc5cvYL9OoTJVGpbcWA6Eo3TKSAJgbpPmTN5VSjKg4TNSDgpkIjcJ0idAIhCCLoBb3UE2UkWS/KgIS23smUcKAIHKZIjVdCSHXB6fs/xjIPbbmHGaWGSsyzm97ZqnuhcUtWxtmucOlwCL8Ha/C7rqN1INljb2zjeU/RybW+T3pVXSeUc2rYzETcBqxM7Ael1mcUrKKsx7F6Smla6roZWNqKfgw62hzHW8nDcH0I6LGzR2Xz5r+mSpVZJbo2q3qqcUzAV0Rb43O0tHQdVZh2g3YNDT16lZqpiBDttSw0rNLiw88hcouaLjLBf+ZXYdrjnzV3TS7BY6GS43O/CrNnEB1ONr9FhZxPZGRlkDRqVE1YItdWclZ3jgL7nhvkFAsXHdRUAy5c7e91VEoY0E+1lbREWDncc3VSOZmh1TIP3Tf6tv4ipkcFcv0Bo5e/hqV+7ywG5G7j5KmXvgaJSNVVNsxvkFM9qOEw31O+9I7zd5KvUiyk94CpmVWzptXVR3iqULnvN+VqOd+yXJHaVCYs0ZWwvG7i3e1EAEzfaRtnj6FbL3m6nXdXNvdV7WfPQm4y8U2n9DzlCM1iSyeXc3fs5OzHGy+TA67G8syuNwxk4qoW+zZBqt/mXJ8d/ZjY1G95wfPWHVUZNwytpHwu+ukkL34HHzU6vdblbcb65bJL0/Mv/kk/q9/qWMrChLrHHuPnEf2a3aIXaf6fy93d+e9k/wDxWdwT9mBjk0jTjGesNpWDkUdJJK76aiAvoCHW6qdSyFT+IOuSWFKK90V+eTzWm0U+j+Z5cyX+zj7L8BMcuO1WMZqlbuY5pxSwO92x+Ij/ADL0Rkrs4yp2c0YpMr5cw3AoALWooA15/wATzdzvqSs6HKRJZanf6/qeo7XVeTXhnb5LYvKdvSpezFFyDfkoJsFQa6+90F5C14uSt3ljvwlMu6oGT1SOlsD5hVxkqVzOQLnhXVATVTd1qAJFwVj3RT9yJRE8xncm3RVKCm1hr3SuZG52mLR94u8llLOzqVLiEZwbXXw2955TmlFtMy1XCxkIfGXWDtLtXN1ZCCSo+4xz7dR0V4ZjNPPTSsMZkZqaSQbkKo+WOJoa5p7rQNFmlzb9bgdVsk9HpVa7bfLHy23LZVZKPiYbEIJImtFS2/VsrD4gsXVODrCb94B92Zmzh7rP4gYZqJ8dO7xMd3hYQQQOtrrWpH2Ph2J+XoVgtRs42ddRg8xa27+/cuKU+eO/Uh13tHfjv4x92dn3mq+pKR2zz++i/wDUbyPdWdI1zpv3R7t/Vh+65bDRQgOBi/4efqw/ccqWlL0ssM9Hsi7oobBpB1N6FZyiiu4ALG0sfj+73UnUD7pWzYJQuqpQ1u1hcu8l17h/T3UqRSRjripyxbNgwEM+zyFm9naL+3KySp0tNHR07Iom6WNFvfzKqg3uvoG2pOjRjCXVGrVJKUm0DuUclQnV2jzIHCdKgbqYJamaN1CdCLJbymStCfShQlMON1ANlJNkAp2Qg8oQCqDwpIUdEAqj5lKjSqMCuQpPKXSolUShCEJHDu1rL0+Uu0zCs/UIeYaqAYZijBctfECXM1W8iTpPQ7dVsIfBXQR1FLKyop5RqjliIc1w9wumTwx1ETo5WNljds5j2gg+4K5q/Ic+TMSrKjCWPqMv1TjNJQsGp9LIeXRjq09Queaxo8nOVWG8ZdV1afivJ9zLW1wsKD6r6ljPFYG/CxNfTEgFrSX9Gt5K2Wog1MEkZa+MjUJCbNA8z5fVafjGPNdXDDcLkbNO5neT1g3ZDH+K/r081xTV9GnTm1gz9KqpIsq+qiwwHW7XOeWN4afL3WOkqp2FktQLzyf1NOPlH4nLIzQsiayokju1gtTwu+849XuWEe+WCZ8khL6mU7uPRaHdW0beWFuy7TMpDKYj3evXOd3v8vRXUUwebfK3k+Z8lgWS6G6A7c7vf/JXome90dJB4ZnDj/02+Z9VjHAnkywlNfM6PVpgi3lcOD/dCrsmFQ41Ug00sX9W3z9Vjg5kmmkguKaPd7uryrsSsneNZ0UsAu48A2Xm1h4RIvYJnQt+1yt1VM3ggj8gsfiNQGvEDXa9Ju934nJxWObTvxKQaXSju6aM9G/i+qw3eXuTuTuVXlwebLsSEKRJcgfVWoluPUqoH2jceS46QqYKFcOOi/Vx2TF9nGxUPIbMR8sDLfVUmknQL7ncpgFcSEhMJLq2D9vdSX8WVOuxTBdB6nV7K6hw6OMxCV73vk+6xgsD15KvGQw3LIY4ZQ3aRgJLwPQ+a2Gholeru2l5dX8kW8q8V0MWX7DyJsoEm6upcHkY6ZrpWxsDwWPefveWyqf0ZEHNjc+QyOOkPa3wB3kvCOjXbbTjjHiyXpo+Jd0dNHLBGRGJGuaS+W+7D5WWLJLnNDdw/Ye6vo6mKOFwpH6ZIgHvaG7usd7lU3d3Hiwb/ZvLZo/rysvfWlOVOkoY2aTaxjfbr5NHjTm05ZKkeFAsPeOe5w+8IgCGny91UaxtFQd/Tlkkjbuc9zd3Nvx6JqeoIjkLgA6F5Bs0udcnkBVIo2gFjNV3nVKH9A4cLYrawt4JSpRSeHv3TPCdSXRsozVBDn6HuMrYxMHavC4dQB5WRK5kDqd4s2mLyT/d1DYqnSsYI45JiY+6a6MF40teOm5VnLi0NHRdzrjqpBZukA20+RPVVlVhTi51WllZXjs12KJOTxEdjJqSmjbNdktPP+6cR99p5HtZZOOpMNMyNlzGSdZvpcy/BN+Atbkx8tZaCFsT7W1ElxA9LrFVFXNUTGYuL6i1nBx2kb5FYtatb2+0My2x4Y+Pc9/Qyk99jb8Qroqdup0glka1zWNDtRN/MrWAO8d5+gWNFZ9iexriTSTG0bzyx34D6+R6rPUFJr7uQkaH/ckHn1B8lgLutVv6ibjhLoXFOmqaLzD6MBgc7xNPDuoWbghuAHWe3o5UqaEt4Gl36FZOlpyTYC1+i2zSNNdRpJEKk8Ir0cDnkMbd9+At8weg+wUjWu/rXbuP8lhstUDe/Mrm3EfW22pbJc+S+htB0uNpT9LJeszWLqu5vlXQqJeiAdlC28x5UHBU8BJumB2VUBlLeFCGqQGTAWCUc7p0IDN4TpG8J0BI3Ka10o5UkeqAgiyhMeClQEEXSk2TpSblAK7lQpI3UIBTyoOyk8qFTAI/govcJkp2USSIJsgu2QeEnCpgqY7GcJpcTw+ohmhY9sjDcEbH381zaswaGjLo2QMjYSCWxtsCRxfzXWHG6wOOYK2qhMkLbOHLR19lq2s6VG9p5S3RfWtf0UsM5XV05c8udu79AsPVUYJcbb+Z6Lca+iLHEWtbzWGqKVriWuGmIbuPn6L5/wBU0eUJtYNpp1VJGqvj+xRd+W6m30wsI3e7z9gnGvDYTDfVXz+KZ34QflWWfFqkNZK24j8MEfkVjJaN7C+VxvK43cVotxaulskXKeR4p+5aynj8TnfeIV1G0YrVtoGO00kPjqpL7ejfcrEATQRtexhfU1Du7gZ1JPVO+sbhcT6WneJu4dpkkB2lqCNz6hgP52WOjRk92TyZDFq8VtW7RYQQjS1o4HorFshvudyrBs2kNYDsOT5lZaiwx9TCyZ8scMb7ht93OtzYBetG2q3E+SkssjJqKyyn3mkXH0V1A4d+2/3Ym3PqVd0mF0ZiEmmaojDiHS3DQ0+duVbnD6qN8sLInSPL7agNiPNXdfSrmjGMms83huecKkZMXWfs4BPimfc+yDIP3rhv8gKvXYSWStM07I2MHdtDRqcT9PVXdNhsFDI2KXRK9t5Hvd91jeBt1K9KOjXNTea5V4v9Ckq0F03MTKdD9PRrQPqk12HKzFVTQ1sewja+W7oZYhZr7fKR0KwBdvYjccqyvLCdpJZeU+6EJqZmaGqfLRys1Fz4HNmYCeAOQsqx3cgyQRuDJHiR+ogEtPNh5LXsGmtiEbLXbJdjh6FXpErqUGGUMqKMvYfNzbra9NrT/DqeG2srz28PgWlWPrYLp076mpqaCQN1iIiOQDclvCt6yuqW01O6B7+5fCCbefW6s8Kr4GVnfVMgE3yvfe1+vCrMxunoYnRwulqRqcLOAawX9PJR9PGvQcp1MOWd87pJ7J9/IrytSwlkbA2vkqTLcCJjbSX6g7W9VfOhjfSRSVIdE6n1NbI5wb4em3K1qmrJaYkwyOjJ2Ok8popzUSFkryQ/Ykm/KxNC+pW9BUuXme+c9N/r/k9pUpSlnODMR4y1r/3sbnO07SxP0lw9Va1WMyTFvdDuGtN9nXJPmSsQ2Q6DG778Liw/RQ+bdWU9Qupw9HzbfX5noqUVvgup6uWo/rJHPI3Bcbq2dJcar79VRMpCR0l1ZNzm+aTyz0Sx0KplulALnC3PRKwF5FlkqKkvuRuF7UqMqjwirZThw1lVFLFMzvIZRpkZxf1HkR5qpSSYtlakexlKcZpmu1ANNpJmdWjoJW9L7O4uCszT0/ibYfVZmjoy7YcnYjo73C3zStNlNpNZLWrUSRVwSaDF6OGopHmeGUAsNrO9iOh6WPC3bCsuljWvnOn+71VPKuXoKIGqELWSPcXeEWuTyT5lbKOF3vRNDp2tNVKi3ZrlzdOb5YhHGyGMMjaGNbwAmao1DyQt3SwsIxbJBsFIchClgDoAul+Up2qQJAumAsoHKZCDZIF045SN5TtFkAw5TKG8KUBI5TJRyp2QAeCkNxa2/mn1KDygIUFoUoQCpDynIslIsgEPKhOlPAQEJHcJ0rlTAFcbJSdkzuiUG6iTFSkbJnfeCU8KjBhMawCPEGOfH4J7X9HLRcSwmSne6ORpFvRdReL26KzrqGGtjLJWA+TuoWu6hpFG8TeMSL2hcypvD6HI6mlJcDawaLNH81YT0nebHjquhYjlR8dzF+8b+q12qwiRjwwsIJNhsuVanwxUjlqJnaV3GXc0fF5KmOcfYGtGISMMVO5/3KdtvFK70A/PjqtdfG2mZFT05eaeBuljpPvPPzPd6uO62ru5qyTECPBH9oNONt3Nb6+V77LH1NB952n+60fzXPLrSatKOMbGRjUTMEJdI36LZMMzBT0dBDcu+0wNexrbbHUQb3+iw89CGk7bNG/urR8Dm8DlYah6WxqOUFuTlFVFhm5vqJIGVzad+knTVxEcaT94KvT40Kx0VWXu7uCzJ4CfDY7agsTQT6aTDqqW7Y4i+lkLhsWHgqyb3mXK2MyFs0E976TcPj4Wxzq1KfJUz6jxnyzun+TLNRTyu5ssEwpZX0x2jfUiSE/5hcfzVaolbPNUPvaF8bqZ7hv3bgdifRYp2IYdSU8UElT34bL3sb2bloFrA/wWGdjU7a2Wake6IzybN87nqFG5vKdCEYTaab6Ldrbp7hGm5PKNspKf7NTUrHytcIZDO5zDdrW28/Va7PUd5M97dgXE/qpxHGKiskma+a8LH921jQGi4HNgsf3t1rGoXNOrGNKlFqMfHqXVOnKLcpPcyFNWyUk7ZYyNTeLjZXM2M1NUwsLhHGeWRjSCsOZLpmThuongbrGwr16cOSEmovsejhFvLQVkwjjY78MoB+qqF+l8o9nLGYvKWUVYerA2QfRwVV9RqeCOHNsvNUnjJIyDJhreL7bFS5/72OxA1HRfyPRYvviXMIOxaR9VP2kkDc35HuFJUivYv6qcGphqPkqmWdbpIzZwVPvdz6KlC4zDE6P543sr4P8AC4eIfnf8ldNpe8cxw+7I3Urp2zbXKRzgohxdxuVUZA5yvYKO1ttzf81dxUl7bWuFkKOnTn1IOaRa0VOQ8X6rYKSlvp/0uH8CranoC9w0jSXcHycFs+EYVJXwsdFGTc6XttsCOoW5aZoNSpNYRaVayisso0lIbiwt5t9VuGXcBuBNO2zPlaeSrrCstx0ZD5rPePl6BZwbWXbtI0KFolOqt/AwFxdOe0R22aABYAbADopDiSlB3ULc0sGLHTXCVvKYC6kkBgbqVAFtlI81IDDhMOCoTgXQgTpUoTAWQABsqiVqcBAAB2UoQgBPpCj6KdHoqMAhQ7hHzKoFQhCAhwS9U6UjdAKRulcLhPYJUAiDwpIsoQCkJCCqpF0hF0KopuF1CcjdKdwoEimRdU3Nuq3ukcLFUwC2exWs9NHMfHG1/wDiCyDhdUXRlQcU9mSTNDxzDaWDEBSNhMD59UsZazwyH5t/xeh5Wr4hhOh58NiONl1upp+/hkiJIa5pBLdjuubZk/pHK9ZPLVQHEMELNbJo23mjI5BHVc/1uzhSzUqR9TxXb3rwMxa1nL1V1NPqsPLfDbrf6rHTUoDybcDYLbcOrcMzPTumwurjqg378f3ZGe7TuFZ1eFua53hI9wuc3Wjxqx56O6fdbmXhW7PqYKPFJ6SjigZHE5zSdD3s1FoPl9VjMQlmrqsyTSOkc0Wu5Z2ehc117HZWT6KwcDyVr1zp9zKKhJtpdi4i4J5SMC+IgX6lXFG0sroC4+Fg1n6C6v30Z4sqNTC6GGplA3EZt+Swz0qafQ9edGOo6p01CJnG/ezSuB8wCB/IqqJrgIpqeNmDUlMLieliZ3gHF5AXK2LHMNljrmynSn6yJKSZd99ZVad4kMzTveF/8FjwHOWSweJjaoOldpY7wC/Uu2AXlQtJ1JpRRVtJFCqZ9toxbfv6N7b+Z0lW9ITPRUkv4mg/osng9K5uHUGsEGN5jP8ABTh2Guhw5kThYxuLf1WaWmTfY8udFvFD+7Jt91/8VUdQHxjc6XcehWWjoC4yAD7zb/kr+HCzKfu3D2D8wspb6LKe2CDqpGtuYaavweqvZwdJQyjzY4amE/UOC2KGi0Ma0cNO3skqMpS1OKQvjjedcDNwLgFkxcB72eVuNPl2eRxDYnEXNjZbTa8M1ZPaJazuorua9FRF3Q+ayFNh2pwsLnyWz0uUZXFvePawfmVsNBglNReIM1O83Lc7Lhhpp1FhGOqXq/tNewfKr5y18oMcY8xutzoqSKiiEUTdDL39ymYLqq3db9a2VG0jiC3MTUqyqdWOBuEyUC6ZZIt2A5U8lQ0J2hVSAAWVRpBCUDzTWtupAlMBZQBdO0XKECQOE6gAKbXCAkC6YBQBZMBwgBosqiUDzTIAUgXQBdMgAcqXcoIsjdQYIQhCmBEcpiLhRYoCEEbIQgEOxQRdMQoQFMi+yUiyqEBKRdAKhSW+ShAIWpS0qoQo0+yFclJzUm3qqxb0SFu1uqgSKLm3SlvKrFqRzd+UBbPYraop2TxujkY2RjhYtcLgq+cw+apOavOcFNYayiqeGaRUdluXHGV9PQmhqJAW/aKSV0cjb82IOy0U9mWfcq1TY8v5gw3MeCtYGjD8yMeyoZY/LUxgk/5mldsfHsqZZ6rHfy+3XswS9232LhVqi7nPZ8sVJwsVNXBDTVDWGSaGKUyhluQHWGr8lomGZyydjryzD8zYVVPB0ljKpocD1BBsQV3vRuCua5y+G/IWe8VmxTEcEZFiU39dU0bzC6Q+bgNifWyxN3pMZR5qMVzeZc0rpp4m9jEjC4KgXinikB6seD/BLLlwyU7wACCL38x1WmZj+CynETv+zOOGlPSLEDIfoHsO3/Kua4v2D9o3ZrI3E3S1dXS04sZaSqfMyNh2PW4FvMLT7m2q26cqls8LummZKnUjN4jP5nUst4e7E6HFcULbRVtc4wA9IowI2/mQ4q2rKPu3nZbfkSHvMmUVMWBhp4w0W4cDuD/FYrG6Tu5iLWC1LUraFanGrFdUXtKbTaZhKWk1u4V/j2ESwZamrIGF0tLNDUkDksZIC/8A03V3g9L3krR6rcJqdseFzRmMSGZhiDPPULfzVNJsYJucl0KVqjWyMKMKa6l1Rglj5O8ZtvY8bK8bgxLpzpJ1yauFv+X8AOHYXTxVDu+qAxoc5wG1hayyRoY3CxjYR5ELqFDQqU4RnJYbXQw8rtptI5/R5emncCyInpfos/QZUbHYzOG3ytWyR07YmhrGhrRwGiwVQMWaoaXQo74yy1ncTkWlPSR0zA2NgDQrhrSAnDN04aCFmlBR6FrkVrbcKs2PwjdKGgKs0cKeCOQaLKowWS2+icCxUiORhvypAQAbp2hVQIaOidrUNamAUgSp07KLOTgW5QA1uydqAFIOyEAAsSnHCgC/smGyAlqYcqALJgNkBKEIQDDhTa6EIAUu5RqQ7lQBCEIUwCDwhCARCCLIQAlIsmQgEIuo0pyLqCLIBFBF0xBugiyAp2shOlIsEApCUi6dQBZAUi25SFu9lWIslc0e5VMEslAt3VJzd/VXBbfcqm5t+FEqUHC4VMtsVXc226TTcoCkG7pmsuVIG6qtAO6jgrkUR8JjCHAgi4IsQeCFUaAVUa25UJJY3KpnMqyjp8LxKqpaeNscDXWa1o2b1stOzLE10pcAASr5+Z48RzHiLQ4Fr6l7W777bD+CxWZJp7tMLGyHVZ2okC3+64xdVI15zhDpl4+Zs1NOKTZc5epmg3IB8lvOAQRVeMU7H2cYmOeB5uWh5cmna13ftYz8IZc+/KzuR8Y+151pWC9ntewAHa1v/pXFhONGtTpy3zJEKycoyfkdTMVtkhiV25lr7KmW+my7Bg13Jb90oLLKs4b7JdIVSmSkG7pgxPpUtapYI5IDFUDUBu9lUA2sqggN3TAItuqjWqWAKG7Jw3ZSG2N7plUEDopA3Rp9043PsgABN0R5pxwhAUeSkCylMBZAA4TWuEAXTIAAumAsEIQAptbqgC6ZACGdFHzKVFgdK7lGyHcqgIQgndCAEIQpgV3KhS7lQgBCEIAUOUoO6AVCYi6U8oBSLKE6gi6AQi6XhOQo0oBUhG6qEWS6UBSckLbdLKuWpC3zCFclBzVScOVcubbgKm5t+ipgkULBM1S5nogNsqYBUZsqgaSCALkgj9EjdyrbG8YpMu4RVYnXSd1S0zNb3Wv6AAdSTYLxqtQpuUtkiUU29jx1h+daePtepMviQGoa6d8sZFnXa7SNvcrqmLR65ibc9FyjM0uXa/tldnGkiewysPfAQ6fGSNx+V1udV2m4HLMAH1D+mpsJsuB0rq3deq+fEc7N7eJtvLLljtuZ6etbhlE57jpBabn0stH+GbO4zv2gQtbMZDSmoJtwQ0lu48+Ff47mvCsVoGNidPLYkGJsL9TtuLWWm9nYxjsjxN+K4fgT4MRqmkyR1DO7jIJuWhvNjsbqzjqdGlqMJTlmEWnlfUrKm5UpRXtM9tO5VJ3CxeT8zMzflqhxZkZg+0M8cJNzG8bOb+YWUcbr6Ho1YV6calN5T3RqEouLcWU3BLpumdyhuy9sFCALFVGhAF0zRdVwADUwG6kNTAWsVIAG+iYCyNKdosEAoBTjbhA3KYCyEGKG77pgLKQLhSBZASjcoTNAQA29kwF0ulOBZAA4Uj1UgXClACEKRygAcpkIQAp1KEaPRRYBCn6II8lQEIQhTAIQhAK7YqLWF0xslQAhCEAIQhACgi6lCAVCZRYICEpG6YhQRdAKhNYIICApWclcDdVVGlAUrbWSuZsqxb5KNJPRAWzmbo0Ku5hRp9kK5KTWm61LtYwSrx/JstHRU76md08Z7qPlwBW5hqdrLcDdWt1QV1RlRk8KSa+ZOE3CSkux5UxvsjxzBaEV1dQRQQF4ZYytc4E8bBYahyXVVla2ClpnyTvFw2NtyfovX1XQwV0XdVMMc8d76JG3F1YUuUsLoq6OspqJkFQ29ns259FoVTgPSatNpzmpeKax8sHvLVL+NZOmouHfOc/A84QZVzFhE7J2x1EU7NmvfC64+ttlkZcZr4qfuK3DaOoeTfvHxHWT5ly9KPY5hIJJKt5qaOYeOJjwejmgrX5fw6hHPobhr3pMyv8ANeb24I0bspGnKIHdiEfaJDoBuBcjhbY4Kuylip4+7hibEwEnSwWF1BjXVNNtHY2lO2lLLiks+ODD1aiqTc13LYMI6JtKrd0UCOyyZ45Ea1PpPRS1huqjWmyFMiWCdosm0eSewQrkUC/onQAPJTYIRIA3TIU2KAhAF0wGykBACEJgAgACyALqVNrFAG9vVSOEDhCAFIHVQpbygGQhCAAE6S9kKLA6X6pkruVQEISk3CAbKYJJsgcJUIAJuhCEAEWQhCAEIQgBCEIAQhCADwgCyEICNKNKlCAUiygs25To5QFMCwsjSqii26ATSo0KqRdRpQCBtk4bwjSFKAghSCWkHlCYi6ASU63l3mVSLSqx54Slt1QFu5iUx2VyWJSwWVQW+kI0eiraPRRouq4BSDN07W7JtO6nSmAIBYplIb6qQB9UwBVNtrqE4F0wBQ3dMjTup0pgEKQL7oA2TJgAhCZVBA25UoQgBCFGpASgcoQogdCVvKZARymbyoQqAk7dEalCEwBEIQqkCNSlCEAIUOQ5CuQ4KlQ1SgyCL7oQhXIIQjf0RDIcoQhCoIUfMpUgCEIUQCEIVcAEIQqAEJgOqN7oCOu6ALqSLqUBGkKUKCbICDyoJQhACWwsmQpAUjySgXT/AHkaUAunZRpCqKLX3QCaQjSEyCLIBdIQRZMhACFHVSgBCgC6YBAQAFJ24Ra26NKAlRa+6lCpkEDYKUIVMlMkalKEIMkg2Ug3SoQZG1KUtyAjUgyMhCEIn//Z", description="Фото в форматі base64"),
     *       )
     *     ),
     *
     *
     *     @SWG\Response(
     *         response=200,
     *         description="Фото успішно додано"
     *     ),
     *
     *     @SWG\Response(
     *         response=401,
     *         description="Необхідна аутентифікація користувача",
     *         examples={"application/json":
     *              {
     *                  "message": "Unauthenticated",
     *              }
     *          }
     *     ),
     *
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом GET, хоча очікується POST.",
     *     ),
     *
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, неіснуючий id, або фото для цього судді вже існує",
     *         examples={"application/json":
     *              {
     *                  "message": "Фото для цього судді вже існує",
     *              }
     *          }
     *     ),
     * )
     */
    /**
     * функція для додавання фото судді
     * @param Request $request
     * @return string
     */
    public function addPhoto(Request $request)
    {
        $sizeInKb = pow(2, 11);
        $request->validate([
            'judge_id' => 'required|int|min:1',
            'photo'    => "required|base64image|base64max:{$sizeInKb}|base64mimes:jpeg,png",
        ]);

        $judge_id = $request->get('judge_id');

        /* @var $judge Judge */
        $judge = Judge::where('judges.id', '=', $judge_id)->first();

        if (empty($judge)) {
            return response()->json(['message' => 'Неіснуючий id судді'], 422);
        }

        if (!empty($judge->photo) && $judge->photo != Judge::NO_PHOTO) {
            return response()->json(['message' => 'Фото даного судді вже існує'], 422);
        }

        $base64 = $request->get('photo');

        $file_extension = 'jpg';
        foreach (['png', 'jpg'] as $extension) {
            if (mb_stripos($base64, $extension) !== false) {
                $file_extension = $extension;
                break;
            }
        }

        $base64 = mb_substr($base64, mb_strpos($base64, ",") + 1);
        $path = "img/judges/{$judge_id}.{$file_extension}";


        Judge::getPhotoStorage()->put(
            $path,
            base64_decode($base64),
            'public'
        );

        $judge->photo = self::AWS_S3.$path;
        // Для отримання лінка використовуємо Judge::getPhotoStorage()->url($path)

        $judge->save();

        return response()->json(['message' => 'Фото успішно додано'], 200);
    }
















    // PRIVATE METHODS


    /**
     * виконується, якщо застосовувалась фільтрація до списку суддів
     * @return array
     */
    private function getFilters() {
        // отримання параметрів, якщо вони були передані
        $regions = Input::has('regions') ? Input::get('regions') : [];
        $instances = Input::has('instances') ? Input::get('instances') : [];
        $jurisdictions = Input::has('jurisdictions') ? Input::get('jurisdictions') : [];
        $sort_order = Input::has('sort') ? intval(Input::get('sort')) : 1;
        $search = Input::has('search') ? trim(Input::get('search')) : '';
        $powers_expired = (Input::has('expired') && Input::get('expired')) ? true : false;

        // приведення всіх фільтрів до Integer
        $int_regions = [];
        $int_instances = [];
        $int_jurisdictions = [];
        foreach($regions as $region) {
            $int_regions[] = intval($region);
        }
        foreach($instances as $instance) {
            $int_instances[] = intval($instance);
        }
        foreach($jurisdictions as $jurisdiction) {
            $int_jurisdictions[] = intval($jurisdiction);
        }

        return (['regions'=>$int_regions,
            'instances'=>$int_instances,
            'jurisdictions'=>$int_jurisdictions,
            'sort_order'=>$sort_order,
            'search'=>$search,
            'powers_expired'=>$powers_expired]);
    }

	/**
	 * виконується, якщо застосовувалась фільтрація до списку суддів
	 * @return array
	 */
	private function countCommonStatistic($adminoffence_statistic, $criminal_statistic, $civil_statistic) {
		// якщо статистики немає
		if (!$adminoffence_statistic && !$criminal_statistic && !$civil_statistic) {
			return NULL;
		}
		$common_statistic = [];
		$all_approved = 0;
		$count_judgements = 0;
		if (array_key_exists('approved_by_appeal', $civil_statistic)) {
			$all_approved += $civil_statistic['approved_by_appeal'];
			$count_judgements++;
		}
		if (array_key_exists('approved_by_appeal', $criminal_statistic)) {
			$all_approved += $criminal_statistic['approved_by_appeal'];
			$count_judgements++;
		}
		if (array_key_exists('approved_by_appeal', $adminoffence_statistic)) {
			$all_approved += $adminoffence_statistic['approved_by_appeal'];
			$count_judgements++;
		}
          if ($count_judgements != 0) {
               $common_statistic['competence'] = intval($all_approved / $count_judgements);
          } else {
               $common_statistic['competence'] = 0;
          }


		$all_approved = 0;
		$count_judgements = 0;
		if (array_key_exists('cases_on_time', $civil_statistic)) {
			$all_approved += $civil_statistic['cases_on_time'];
			$count_judgements++;
		}
		if (array_key_exists('cases_on_time', $criminal_statistic)) {
			$all_approved += $criminal_statistic['cases_on_time'];
			$count_judgements++;
		}
		if (array_key_exists('cases_on_time', $adminoffence_statistic)) {
			$all_approved += $adminoffence_statistic['cases_on_time'];
			$count_judgements++;
		}
          if ($count_judgements != 0) {
               $common_statistic['timeliness'] = intval($all_approved / $count_judgements);
          } else {
               $common_statistic['timeliness'] = 0;
          }


		return $common_statistic;
	}
}
