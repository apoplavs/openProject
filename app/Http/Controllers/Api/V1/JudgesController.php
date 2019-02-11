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

/**
 * Class JudgesController
 * @package Toecyd\Http\Controllers\Judges
 */
class JudgesController extends Controller
{
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
        $old_status = Judge::find($id)->status;

        // Додати в чергу відправку повідомлень всім хто:
		// підписаний на судові засідання даного судді
		SendNotification3::dispatch($id, $old_status, $new_status)->delay(now()->addMinute());
		// відстежує даного суддю
        SendNotification1::dispatch($id, $old_status, $new_status)->delay(now()->addMinute());

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
        $sizeInKb = pow(2, 10);
        $request->validate([
            'judge_id' => 'int|min:1',
            'photo'    => "required|image|max:{$sizeInKb}|mimes:jpeg,png",
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

        $photo_file = $request->file('photo');
        $file_extension = $photo_file->extension();

        // Нюанс роботи методу extension(): він розпізнає розширення 'jpg' як 'jpeg'.
        if ($file_extension == 'jpeg') {
            $file_extension = 'jpg';
        }

        $judge->photo = "img/judges/{$judge_id}.{$file_extension}";

        Judge::getPhotoStorage()->put($judge->photo, $photo_file);

        $judge->save();

        return response()->json([], 200);
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
