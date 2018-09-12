<?php

namespace Toecyd\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Toecyd\Http\Controllers\Controller;
use Toecyd\Judge;
use Toecyd\JudgesStatistic;
use Toecyd\UserBookmarkJudge;
use Toecyd\UserHistory;
use Toecyd\UsersLikesJudge;
use Toecyd\UsersUnlikesJudge;

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
	 *   	},
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/Content-Type",
	 *     ),
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/X-Requested-With",
	 *     ),
	 *
	 *     @SWG\Parameter(
	 *     name="regions[]",
	 *     in="query",
	 *     description="Перелік регіонів (областей), в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись по всіх регіонах. Коди регіонів:
	 *   2 - Вінницька область
	 *	 3 - Волинська область
	 *	 4 - Дніпропетровська область
	 *	 5 - Донецька область
	 *	 6 - Житомирська область
	 *	 7 - Закарпатська область
	 *	 8 - Запорізька область
	 *	 9 - Івано-Франківська область
	 *	 10 - Київська область
	 *	 11 - Кіровоградська область
	 *	 12 - Луганська область
	 *	 13 - Львівська область
	 *	 14 - Миколаївська область
	 *	 15 - Одеська область
	 *	 16 - Полтавська область
	 *	 17 - Рівненська область
	 *	 18 - Сумська область
	 *	 19 - Тернопільська область
	 *	 20 - Харківська область
	 *	 21 - Херсонська область
	 *	 22 - Хмельницька область
	 *	 23 - Черкаська область
	 *	 24 - Чернівецька область
	 *	 25 - Чернігівська область
	 *	 26 - м. Київ
	НАПРИКЛАД: 'host/api/v1/judges/list?regions[]=2&regions[]=3&regions[]=4' - означає, що потрібно отримати всіх суддів з Вінницької, Волинської і Дніпропетровської областей",
	 *     type="array",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     @SWG\Items(
	 *            type="integer",
	 *     		  example="7"
	 *          )
	 *     ),
	 *
	 * 	  @SWG\Parameter(
	 *     name="instances[]",
	 *     in="query",
	 *     description="Інстанції судів в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись у всіх інстанціях. Коди інстанцій:
	 *   1 - Касаційна інстанція
	 *	 2 - Апеляційна інстанція
	 *	 3 - Перша інстанція",
	 *     type="array",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     @SWG\Items(
	 *            type="integer",
	 *     		  example="2"
	 *          )
	 *     ),
	 *
	 * 	  @SWG\Parameter(
	 *     name="jurisdictions[]",
	 *     in="query",
	 *     description="Юрисдикції судів в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись у всіх юрисдикціях. Коди юрисдикцій:
	 *   1 - Загальна юрисдикція суду
	 *	 2 - Адміністративна юрисдикція суду
	 *	 3 - Господарська юрисдикція суду",
	 *     type="array",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     @SWG\Items(
	 *            type="integer",
	 *     		  example="1"
	 *          )
	 *     ),
	 *
	 * 	  @SWG\Parameter(
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
	 * 	  @SWG\Parameter(
	 *     name="sort",
	 *     in="query",
	 *     description="Тип сортування при поверненні результатів. Коди типів:
	 *   1 - Сортувати за прізвищем 'А->Я'
	 *	 2 - Сортувати за прізвищем 'Я->А'
	 *	 3 - Сортувати за рейтингом 'низький->високий'
	 *	 4 - Сортувати за рейтингом 'високий->низький'",
	 *     type="integer",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     minimum=1,
	 *     maximum=4,
	 *     allowEmptyValue=false
	 *     ),
	 *
	 * 	  @SWG\Parameter(
	 *     name="expired",
	 *     in="query",
	 *     description="Якщо переданий цей параметр, то в результати пошуку будуть включені судді зі статусом 'закінчились повноваження'",
	 *     type="integer",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     default=1,
	 *     allowEmptyValue=false
	 *     ),
	 *
	 * 	   @SWG\Parameter(
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
	 *     	   @SWG\Schema(
	 *		   @SWG\Property(property="current_page", type="integer", description="Поточна сторінка пошуку"),
	 *		   @SWG\Property(property="data", type="json", description="Список суддів"),
	 *     	   @SWG\Property(property="id", type="string", description="id судді"),
	 *     	   @SWG\Property(property="court_name", type="string", description="Назва суду, в якому даний суддя працює"),
	 *     	   @SWG\Property(property="surname", type="string", description="Прізвище судді"),
	 *     	   @SWG\Property(property="name", type="string", description="Ім'я судді"),
	 *     	   @SWG\Property(property="patronymic", type="string", description="По батькові судді"),
	 *     	   @SWG\Property(property="photo", type="string", description="URL фото судді"),
	 *     	   @SWG\Property(property="status", type="integer", description="Id поточного статусу судді
	 * 	 1 - суддя на роботі
	 *	 2 - На лікарняному
	 *	 3 - У відпустці
	 *	 4 - Відсуній на робочому місці з інших причин
	 *	 5 - Припинено повноваження"),
	 *     	   @SWG\Property(property="updated_status", type="string", description="Дата останнього оновлення статусу"),
	 *     	   @SWG\Property(property="due_date_status", type="string", description="Дата дії статусу (до якого часу даний статус буде діяти)"),
	 *     	   @SWG\Property(property="rating", type="integer", description="Місце в рейтингу серед усіх суддів"),
	 *     	   @SWG\Property(property="is_bookmark", type="integer", description="Чи знаходиться в закладках в поточного користувача 1 - так,  0 - ні"),
	 *     	   @SWG\Property(property="from", type="integer", description="Загальний номер результату в пошуку, який є першим на даній сторінці"),
	 *     	   @SWG\Property(property="last_page", type="integer", description="Остання сторінка в пошуку"),
	 *     	   @SWG\Property(property="path", type="string", description="URL поточного ресурсу"),
	 *     	   @SWG\Property(property="per_page", type="integer", description="Кількість результатів на поточній сторінці"),
	 *     	   @SWG\Property(property="total", type="integer", description="Загальна кількість результатів"),
	 *     	   @SWG\Property(property="to", type="integer", description="Загальний номер результату в пошуку, який є останнім на даній сторінці"),
	 *     	   ),
	 *     	   examples={"application/json":
	 *              {
	 *					"current_page": 1,
	 *					"data": {
	 *					{
	 *					"id": 9087,
	 *					"court_name": "Господарський суд Київської області",
	 *					"surname": "Євграфова",
	 *					"name": "Є",
	 *					"patronymic": "П",
	 *					"photo": "/img/judges/no_photo.jpg",
	 *					"status": 1,
	 *					"updated_status": "08.06.2018",
	 *					"due_date_status": null,
	 *					"rating": 0,
	 *					"is_bookmark": 0
	 *					},
	 *					{
	 *					"id": 1518,
	 *					"court_name": "Шосткинський міськрайонний суд Сумської області",
	 *					"surname": "Євдокімова",
	 *					"name": "Олена",
	 *					"patronymic": "Павлівна",
	 *					"photo": "/img/judges/no_photo.jpg",
	 *					"status": 3,
	 *					"updated_status": "21.07.2018",
	 *					"due_date_status": "13.06.2018",
	 *					"rating": 0,
	 *					"is_bookmark": 0
	 *					},
	 *					{
	 *					"id": 5793,
	 *					"court_name": "Соснівський районний суд м. Черкаси",
	 *					"surname": "Євтушенко",
	 *					"name": "П",
	 *					"patronymic": "М",
	 *					"photo": "/img/judges/no_photo.jpg",
	 *					"status": 1,
	 *					"updated_status": "23.05.2018",
	 *					"due_date_status": null,
	 *					"rating": 0,
	 *					"is_bookmark": 0
	 *					}
	 *					},
	 *					"first_page_url": "http://toecyd.top/api/v1/judges/list?page=1",
	 *					"from": 1,
	 *					"last_page": 745,
	 *					"last_page_url": "http://toecyd.top/api/v1/judges/list?page=745",
	 *					"next_page_url": "http://toecyd.top/api/v1/judges/list?page=2",
	 *					"path": "http://toecyd.local/api/v1/judges/list",
	 *					"per_page": 10,
	 *					"prev_page_url": null,
	 *					"to": 10,
	 *					"total": 7445
	 *				}
	 *     		}
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Необхідна аутентифікація користувача, можливо токен не існує, або анульований",
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "Unauthenticated",
	 *              }
	 *     		}
	 *     ),
	 *     @SWG\Response(
	 *         response=405,
	 *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується GET.",
	 *     ),
	 *     @SWG\Response(
	 *         response=422,
	 *         description="Передані не валідні дані, у відповіді буде зазначена причина",
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "The given data was invalid.",
	 *                  "errors": {
	 *                  	"regions.0": {
	 *                                "максимальне значення для regions.0 = 26."
	 * 						},
	 *     					"instances.0": {
	 *                                 "мінімальне значення для instances.0 = 1."
	 * 						},
	 *     					"instances.1": {
	 *     								"максимальне значення для instances.1 = 3."
	 * 						}
	 *     				}
	 *              }
	 *     		}
	 *     )
	 * )
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
			'sort' => 'numeric|min:1|max:4'
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
	 *     	ref="#/parameters/Content-Type",
	 *     ),
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/X-Requested-With",
	 *     ),
	 *
	 *     @SWG\Parameter(
	 *     name="regions[]",
	 *     in="query",
	 *     description="Перелік регіонів (областей), в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись по всіх регіонах. Коди регіонів:
	 *   2 - Вінницька область
	 *	 3 - Волинська область
	 *	 4 - Дніпропетровська область
	 *	 5 - Донецька область
	 *	 6 - Житомирська область
	 *	 7 - Закарпатська область
	 *	 8 - Запорізька область
	 *	 9 - Івано-Франківська область
	 *	 10 - Київська область
	 *	 11 - Кіровоградська область
	 *	 12 - Луганська область
	 *	 13 - Львівська область
	 *	 14 - Миколаївська область
	 *	 15 - Одеська область
	 *	 16 - Полтавська область
	 *	 17 - Рівненська область
	 *	 18 - Сумська область
	 *	 19 - Тернопільська область
	 *	 20 - Харківська область
	 *	 21 - Херсонська область
	 *	 22 - Хмельницька область
	 *	 23 - Черкаська область
	 *	 24 - Чернівецька область
	 *	 25 - Чернігівська область
	 *	 26 - м. Київ
	НАПРИКЛАД: 'host/api/v1/judges/list?regions[]=2&regions[]=3&regions[]=4' - означає, що потрібно отримати всіх суддів з Вінницької, Волинської і Дніпропетровської областей",
	 *     type="array",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     @SWG\Items(
	 *            type="integer",
	 *     		  example="7"
	 *          )
	 *     ),
	 *
	 * 	  @SWG\Parameter(
	 *     name="instances[]",
	 *     in="query",
	 *     description="Інстанції судів в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись у всіх інстанціях. Коди інстанцій:
	 *   1 - Касаційна інстанція
	 *	 2 - Апеляційна інстанція
	 *	 3 - Перша інстанція",
	 *     type="array",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     @SWG\Items(
	 *            type="integer",
	 *     		  example="2"
	 *          )
	 *     ),
	 *
	 * 	  @SWG\Parameter(
	 *     name="jurisdictions[]",
	 *     in="query",
	 *     description="Юрисдикції судів в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись у всіх юрисдикціях. Коди юрисдикцій:
	 *   1 - Загальна юрисдикція суду
	 *	 2 - Адміністративна юрисдикція суду
	 *	 3 - Господарська юрисдикція суду",
	 *     type="array",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     @SWG\Items(
	 *            type="integer",
	 *     		  example="1"
	 *          )
	 *     ),
	 *
	 * 	  @SWG\Parameter(
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
	 * 	  @SWG\Parameter(
	 *     name="sort",
	 *     in="query",
	 *     description="Тип сортування при поверненні результатів. Коди типів:
	 *   1 - Сортувати за прізвищем 'А->Я'
	 *	 2 - Сортувати за прізвищем 'Я->А'
	 *	 3 - Сортувати за рейтингом 'низький->високий'
	 *	 4 - Сортувати за рейтингом 'високий->низький'",
	 *     type="integer",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     minimum=1,
	 *     maximum=4,
	 *     allowEmptyValue=false
	 *     ),
	 *
	 * 	  @SWG\Parameter(
	 *     name="expired",
	 *     in="query",
	 *     description="Якщо переданий цей параметр, то в результати пошуку будуть включені судді зі статусом 'закінчились повноваження'",
	 *     type="integer",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     default=1,
	 *     allowEmptyValue=false
	 *     ),
	 *
	 * 	   @SWG\Parameter(
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
	 *     	   @SWG\Schema(
	 *		   @SWG\Property(property="current_page", type="integer", description="Поточна сторінка пошуку"),
	 *		   @SWG\Property(property="data", type="json", description="Список суддів"),
	 *     	   @SWG\Property(property="court_name", type="string", description="Назва суду, в якому даний суддя працює"),
	 *     	   @SWG\Property(property="surname", type="string", description="Прізвище судді"),
	 *     	   @SWG\Property(property="name", type="string", description="Ім'я судді"),
	 *     	   @SWG\Property(property="patronymic", type="string", description="По батькові судді"),
	 *     	   @SWG\Property(property="photo", type="string", description="URL фото судді"),
	 *     	   @SWG\Property(property="status", type="integer", description="Id поточного статусу судді
	 * 	 1 - суддя на роботі
	 *	 2 - На лікарняному
	 *	 3 - У відпустці
	 *	 4 - Відсуній на робочому місці з інших причин
	 *	 5 - Припинено повноваження"),
	 *     	   @SWG\Property(property="updated_status", type="string", description="Дата останнього оновлення статусу"),
	 *     	   @SWG\Property(property="due_date_status", type="string", description="Дата дії статусу (до якого часу даний статус буде діяти)"),
	 *     	   @SWG\Property(property="rating", type="integer", description="Місце в рейтингу серед усіх суддів"),
	 *     	   @SWG\Property(property="from", type="integer", description="Загальний номер результату в пошуку, який є першим на даній сторінці"),
	 *     	   @SWG\Property(property="last_page", type="integer", description="Остання сторінка в пошуку"),
	 *     	   @SWG\Property(property="path", type="string", description="URL поточного ресурсу"),
	 *     	   @SWG\Property(property="per_page", type="integer", description="Кількість результатів на поточній сторінці"),
	 *     	   @SWG\Property(property="total", type="integer", description="Загальна кількість результатів"),
	 *     	   @SWG\Property(property="to", type="integer", description="Загальний номер результату в пошуку, який є останнім на даній сторінці"),
	 *     	   ),
	 *     	   examples={"application/json":
	 *              {
	 *					"current_page": 1,
	 *					"data": {
	 *					{
	 *            		"id": 4012,
	 *					"court_name": "Господарський суд Київської області",
	 *					"surname": "Євграфова",
	 *					"name": "Є",
	 *					"patronymic": "П",
	 *					"photo": "/img/judges/no_photo.jpg",
	 *					"status": 1,
	 *					"updated_status": "08.06.2018",
	 *					"due_date_status": null,
	 *					"rating": 0
	 *					},
	 *					{
	 *          		"id": 114,
	 *					"court_name": "Шосткинський міськрайонний суд Сумської області",
	 *					"surname": "Євдокімова",
	 *					"name": "Олена",
	 *					"patronymic": "Павлівна",
	 *					"photo": "/img/judges/no_photo.jpg",
	 *					"status": 3,
	 *					"updated_status": "21.07.2018",
	 *					"due_date_status": "13.06.2018",
	 *					"rating": 0
	 *					},
	 *					{
	 *     				"id": 1518,
	 *					"court_name": "Соснівський районний суд м. Черкаси",
	 *					"surname": "Євтушенко",
	 *					"name": "П",
	 *					"patronymic": "М",
	 *					"photo": "/img/judges/no_photo.jpg",
	 *					"status": 1,
	 *					"updated_status": "23.05.2018",
	 *					"due_date_status": null,
	 *					"rating": 0
	 *					}
	 *					},
	 *					"first_page_url": "http://toecyd.top/api/v1/judges/list?page=1",
	 *					"from": 1,
	 *					"last_page": 745,
	 *					"last_page_url": "http://toecyd.top/api/v1/judges/list?page=745",
	 *					"next_page_url": "http://toecyd.top/api/v1/judges/list?page=2",
	 *					"path": "http://toecyd.local/api/v1/judges/list",
	 *					"per_page": 10,
	 *					"prev_page_url": null,
	 *					"to": 10,
	 *					"total": 7445
	 *				}
	 *     		}
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=405,
	 *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується GET.",
	 *     ),
	 *     @SWG\Response(
	 *         response=422,
	 *         description="Передані не валідні дані, у відповіді буде зазначена причина",
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "The given data was invalid.",
	 *                  "errors": {
	 *                  	"search": {
	 *                                "search може містити лише літери."
	 * 						},
	 *     					"instances.0": {
	 *                                 "максимальне значення для instances.0 = 3."
	 * 						},
	 *     					"instances.1": {
	 *     								"instances.1 повинен бути числом."
	 * 						}
	 *     				}
	 *              }
	 *     		}
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
			'sort' => 'numeric|min:1|max:4'
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		return response()->json(false);
    	$judge = Judge::getJudgeData($id);
    	$statistic = JudgesStatistic::getStatistic($id);
    	$liked = UsersLikesJudge::isLikedJudge($id);
		$unliked = UsersUnlikesJudge::isUnlikedJudge($id);
		
		// вносим в історію переглядів
		if (Auth::check()) {
		UserHistory::addToHistory($id);
		}
        return (view('judges.judge', compact('judge', 'statistic', 'liked', 'unliked')));
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
	 * Display a listing of the resource.
	 *
	 * @SWG\Get(
	 *     path="/judges/autocomplete",
	 *     summary="Перелік ПІБ суддів для поля автодоповнення",
	 *     description="Отримати швидкий перелік ПІБ суддів для 'живого пошуку' Всі результати пошуку повертаються по 5 шт.",
	 *     operationId="judges-autocomplete",
	 *     produces={"application/json"},
	 *     tags={"Судді"},
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/Content-Type",
	 *     ),
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/X-Requested-With",
	 *     ),
	 *
	 * 	  @SWG\Parameter(
	 *     name="search",
	 *     in="query",
	 *     description="Пошук за прізвищем судді. Повинен містити від 1 до 20 символів. Будуть повернуті всі судді, початок прізвища яких співпадає з заданим параметром.
	НАПРИКЛАД: 'host/api/v1/judges/autocomplete?search=Мельн' - означає, що потрібно отримати суддів прізвище яких починається на 'Мельн%'",
	 *     type="string",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *	   required=true,
	 *     minLength=1,
	 *     maxLength=20,
	 *     allowEmptyValue=false
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=200,
	 *         description="ОК",
	 *     	   @SWG\Schema(
	 *     	   @SWG\Property(property="surname", type="string", description="Прізвище судді"),
	 *     	   @SWG\Property(property="name", type="string", description="Ім'я судді"),
	 *     	   @SWG\Property(property="patronymic", type="string", description="По батькові судді"),
	 *     	   ),
	 *     	   examples={"application/json":
	 *              {
	 *					{
	 *						"surname": "Борсук",
	 *						"name": "Петро",
	 *						"patronymic": "Павлович"
	 *						},
	 *						{
	 *						"surname": "Коваленко",
	 *						"name": "Валентина",
	 *						"patronymic": "Петрівна"
	 *						},
	 *						{
	 *						"surname": "Петрова",
	 *						"name": "О",
	 *						"patronymic": "Ф"
	 *						},
	 *						{
	 *						"surname": "Галинчук",
	 *						"name": "Володимир",
	 *						"patronymic": "Петрович"
	 *						},
	 *						{
	 *						"surname": "Борисенко",
	 *						"name": "Петро",
	 *						"patronymic": "Іванович"
	 *						}
	 *					}
	 *     			}
	 *     ),
	 *     @SWG\Response(
	 *         response=422,
	 *         description="Передані не валідні дані, у відповіді буде зазначена причина",
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "The given data was invalid.",
	 *                  "errors": {
	 *                  	"search": {
	 *     							"search може містити лише літери."
	 * 						}
	 *     				}
	 *              }
	 *     		}
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=405,
	 *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується GET.",
	 *     )
	 * )
	 */
	public function autocomplete(Request $request) {
		$search = Input::has('search') ? Input::get('search') : '';
		
		// валідація фільтрів
		$request->validate([
			'search' => 'string|alpha|min:1|max:20'
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
	 *   	},
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/Content-Type",
	 *     ),
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/X-Requested-With",
	 *     ),
	 *
	 * 	  @SWG\Parameter(
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
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "Закладка успішно створена"
	 *              }
	 *     		}
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Необхідна аутентифікація користувача",
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "Unauthenticated",
	 *              }
	 *     		}
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
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "Неіснуючий id",
	 *              }
	 *     		}
	 *     ),
	 * )
	 */
	public function addJudgeBookmark($id) {
		$id = intval($id);
		if (!Judge::checkJudgeById($id)) {
			return response()->json([
				'message' => 'Неіснуючий id'
			], 422);
		}
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
	 *   	},
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/Content-Type",
	 *     ),
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/X-Requested-With",
	 *     ),
	 *
	 * 	  @SWG\Parameter(
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
	 *         description="Закладка упішно видалена"
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Необхідна аутентифікація користувача",
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "Unauthenticated",
	 *              }
	 *     		}
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
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "Закладки не існує",
	 *              }
	 *     		}
	 *     ),
	 * )
	 */
	public function delJudgeBookmark($id) {
		$id = intval($id);
		if (!Judge::checkJudgeById($id)) {
			return response()->json([
				'message' => 'Неіснуючий id'
			], 422);
		}
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
	 *   	},
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/Content-Type",
	 *     ),
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/X-Requested-With",
	 *     ),
	 *
	 * 	  @SWG\Parameter(
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
	 *	Дата дії може бути передана для статусів 2-4.  Для статусів 1,5 дата дії не враховується",
	 *     @SWG\Schema(
	 *          type="object",
	 *     		required={"set_status"},
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
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "Статус успішно оновлено"
	 *              }
	 *     		}
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Необхідна аутентифікація користувача",
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "Unauthenticated",
	 *              }
	 *     		}
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
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "The given data was invalid.",
	 *     				"errors": {
	 *						"due_date": {
	 *						"due date не валідна дата.",
	 *						"due date не відповідає формату Y-m-d.",
	 *						"due date повинна бути дата, що пізніша або рівна today."
	 *						}
	 *					}
	 *              }
	 *     		}
	 *     ),
	 * )
	 */
	public function updateJudgeStatus(Request $request, $id) {
		// валідація вхідних даних
		$request->validate([
			'set_status' => 'required|integer|between:1,5',
			'due_date' => 'date|date_format:Y-m-d|after_or_equal:today|before_or_equal:+1 month|nullable'
		]);
		// перевірка чи існує такий id
		if (!Judge::checkJudgeById($id)) {
			return response()->json([
				'message' => 'Неіснуючий id'
			], 422);
		}
		$new_status = intval($request->set_status);
		// якщо due_date не передана, або передано статуси 1,5 due_date=null
		$due_date = $request->due_date ?? NULL;
		if ($new_status == 1 || $new_status == 5) {
			$due_date = NULL;
		}
		// оновлення статусу судді
		Judge::setNewStatus($id, $new_status, $due_date);
		
		return response()->json([
			'message' => 'Статус успішно оновлено'
		], 200);
	}
	
	
	
	
	
	/**
	 * Поставити лайк судді
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function putLike($id) {
		// перевіряємо чи користувач вже ставив лайк
		$is_liked = UsersLikesJudge::isLikedJudge($id);
		
		// якщо ставив - то прибираємо, в іншому випадку ставимо
		if ($is_liked) {
			$judge_data = UsersLikesJudge::deleteLike($id);
			return (view('judges.judge-likes-unlikes')
				->with(['judge' => $judge_data,
					'liked' => false,
					'unliked' => false
				]));
		} else {
			$judge_data = UsersLikesJudge::putLike($id);
			return (view('judges.judge-likes-unlikes')
				->with(['judge' => $judge_data,
					'liked' => true,
					'unliked' => false
				]));
		}
	}
	
	/**
	 * Поставити дизлайк судді
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function putUnlike($id) {
		// перевіряємо чи користувач вже ставив дизлайк
		$is_unliked = UsersUnlikesJudge::isUnlikedJudge($id);
		
		// якщо ставив - то прибираємо, в іншому випадку ставимо
		if ($is_unliked) {
			$judge_data = UsersUnlikesJudge::deleteUnlike($id);
			return (view('judges.judge-likes-unlikes')
				->with(['judge' => $judge_data,
					'liked' => false,
					'unliked' => false
				]));
		} else {
			$judge_data = UsersUnlikesJudge::putUnlike($id);
			return (view('judges.judge-likes-unlikes')
				->with(['judge' => $judge_data,
					'liked' => false,
					'unliked' => true
				]));
		}
	}
	
	
	/**
	 * функція для додавання фото судді
	 * ПОКИ ЩО НЕ ВИКОРИСТОВУЄЬСЯ
	 * @param Request $request
	 * @return string
	 */
	public function addPhoto(Request $request) {
		return json_encode(Input::all());
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
		$search = Input::has('search') ? Input::get('search') : '';
		$powers_expired = Input::has('expired') ? true : false;
		
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
}
