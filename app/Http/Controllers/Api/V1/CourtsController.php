<?php

namespace Toecyd\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Toecyd\Court;
use Toecyd\Http\Controllers\Controller;
use Toecyd\UserBookmarkCourt;

/**
 * Class CourtsController
 * @package Toecyd\Http\Controllers\Api\V1
 */
class CourtsController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @SWG\Get(
	 *     path="/courts/list",
	 *     summary="Отримати список судових установ",
	 *     description="Отримати список судових установ за заданими параметрами можна передавши параметри (фільтри) пошуку описані нижче. Всі параметри можна використовувати як окремо, так і суміщати декілька параметрів одночасно. Всі результати пошуку повертаються по 10 шт. на одній сторінці",
	 *     operationId="courts-list",
	 *     produces={"application/json"},
	 *     tags={"Суди"},
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
	НАПРИКЛАД: 'host/api/v1/courts/list?regions[]=2&regions[]=3&regions[]=4' - означає, що потрібно отримати всі суди з Вінницької, Волинської і Дніпропетровської областей",
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
	 *     description="Інстанції в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись у всіх інстанціях. Коди інстанцій:
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
	 *     description="Юрисдикції в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись у всіх юрисдикціях. Коди юрисдикцій:
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
	 *     description="Пошук за назвою суду. Повинен містити від 1 до 20 символів. Будуть повернуті всі суди початок назви яких співпадає з заданим параметром.
	НАПРИКЛАД: 'host/api/v1/courts/list?search=Дніпр' - означає, що потрібно отримати всі суди назва яких починається на 'Дніпр%'",
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
	 *   1 - Сортувати за назвою 'А->Я'
	 *	 2 - Сортувати за назвою 'Я->А'
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
	 *		   @SWG\Property(property="data", type="json", description="Список судових установ"),
	 *     	   @SWG\Property(property="court_code", type="string", description="Код суду"),
	 *     	   @SWG\Property(property="court_name", type="string", description="Назва суду"),
	 *     	   @SWG\Property(property="instance", type="string", description="Інстанція суду [Перша, Апеляційна, Касаційна]"),
	 *     	   @SWG\Property(property="region", type="string", description="Регіон суду"),
	 *     	   @SWG\Property(property="jurisdiction", type="string", description="Юрисдикція суду [Загальна, Адміністративна, Господарська]"),
	 *     	   @SWG\Property(property="address", type="string", description="Адреса суду"),
	 *     	   @SWG\Property(property="head_judge", type="string", description="ПІБ головуючого судді  || null якщо невідомо"),
	 *     	   @SWG\Property(property="rating", type="integer", description="Місце в рейтингу серед усіх судових установ"),
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
	 *					"court_code": 2390,
	 *					"court_name": "Апеляційний суд Черкаської області",
	 *					"instance": "Апеляційна",
	 *					"region": "Черкаська область",
	 *					"jurisdiction": "Загальна",
	 *					"address": "вул.Гоголя, 316;  вул. Верхня Горова, 29, м.Черкаси, 18001",
	 *					"head_judge": null,
	 *					"rating": 142,
	 *					"is_bookmark": 0
	 *					},
	 *					{
	 *					"court_code": 2690,
	 *					"court_name": "Апеляційний суд міста Києва",
	 *					"instance": "Апеляційна",
	 *					"region": "м. Київ",
	 *					"jurisdiction": "Загальна",
	 *					"address": "03110, м. Київ, вул. Солом'янська, 2-А",
	 *					"head_judge": null,
	 *					"rating": 0,
	 *					"is_bookmark": 0
	 *					},
	 *					{
	 *					"court_code": 607,
	 *					"court_name": "Ємільчинський районний суд Житомирської області",
	 *					"instance": "Перша",
	 *					"region": "Житомирська область",
	 *					"jurisdiction": "Загальна",
	 *					"address": "11200, смт. Ємільчине, вул Незалежності, 2",
	 *					"head_judge": null,
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
	public function index(Request $request)	{
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
		$courts_list = Court::getCourtsList($filters['regions'], $filters['instances'],
			$filters['jurisdictions'], $filters['sort_order'], $filters['search']);
		
		return response()->json($courts_list);
	}
	
	
	
	
	
	
	/**
	 * Display a listing of the resource.
	 *
	 * @SWG\Get(
	 *     path="/guest/courts/list",
	 *     summary="Отримати список судових установ для незареєстрованого користувача",
	 *     description="Даний маршрут працює так же як /courts/list, за винятком того, що не вимагає авторизації користувача і не повертає даних що стосуються користувача (напр. is_bookmark)",
	 *     operationId="guest-courts-list",
	 *     produces={"application/json"},
	 *     tags={"Суди"},
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
	НАПРИКЛАД: 'host/api/v1/courts/list?regions[]=2&regions[]=3&regions[]=4' - означає, що потрібно отримати всі суди з Вінницької, Волинської і Дніпропетровської областей",
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
	 *     description="Інстанції в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись у всіх інстанціях. Коди інстанцій:
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
	 *     description="Юрисдикції в яких потрібно здійснювати пошук. Відсутність даного параметру означає, що пошук буде відбуватись у всіх юрисдикціях. Коди юрисдикцій:
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
	 *     description="Пошук за назвою суду. Повинен містити від 1 до 20 символів. Будуть повернуті всі суди початок назви яких співпадає з заданим параметром.
	НАПРИКЛАД: 'host/api/v1/courts/list?search=Дніпр' - означає, що потрібно отримати всі суди назва яких починається на 'Дніпр%'",
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
	 *   1 - Сортувати за назвою 'А->Я'
	 *	 2 - Сортувати за назвою 'Я->А'
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
	 *		   @SWG\Property(property="data", type="json", description="Список судових установ"),
	 *     	   @SWG\Property(property="court_code", type="string", description="Код суду"),
	 *     	   @SWG\Property(property="court_name", type="string", description="Назва суду"),
	 *     	   @SWG\Property(property="instance", type="string", description="Інстанція суду [Перша, Апеляційна, Касаційна]"),
	 *     	   @SWG\Property(property="region", type="string", description="Регіон суду"),
	 *     	   @SWG\Property(property="jurisdiction", type="string", description="Юрисдикція суду [Загальна, Адміністративна, Господарська]"),
	 *     	   @SWG\Property(property="address", type="string", description="Адреса суду"),
	 *     	   @SWG\Property(property="head_judge", type="string", description="ПІБ головуючого судді  || null якщо невідомо"),
	 *     	   @SWG\Property(property="rating", type="integer", description="Місце в рейтингу серед усіх судових установ"),
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
	 *					"court_code": 2390,
	 *					"court_name": "Апеляційний суд Черкаської області",
	 *					"instance": "Апеляційна",
	 *					"region": "Черкаська область",
	 *					"jurisdiction": "Загальна",
	 *					"address": "вул.Гоголя, 316;  вул. Верхня Горова, 29, м.Черкаси, 18001",
	 *					"head_judge": null,
	 *					"rating": 142
	 *					},
	 *					{
	 *					"court_code": 2690,
	 *					"court_name": "Апеляційний суд міста Києва",
	 *					"instance": "Апеляційна",
	 *					"region": "м. Київ",
	 *					"jurisdiction": "Загальна",
	 *					"address": "03110, м. Київ, вул. Солом'янська, 2-А",
	 *					"head_judge": null,
	 *					"rating": 0
	 *					},
	 *					{
	 *					"court_code": 607,
	 *					"court_name": "Ємільчинський районний суд Житомирської області",
	 *					"instance": "Перша",
	 *					"region": "Житомирська область",
	 *					"jurisdiction": "Загальна",
	 *					"address": "11200, смт. Ємільчине, вул Незалежності, 2",
	 *					"head_judge": null,
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
	public function indexGuest(Request $request) {
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
		$courts_list = Court::getCourtsListGuest($filters['regions'], $filters['instances'],
			$filters['jurisdictions'],	$filters['sort_order'], $filters['search']);
		
		return response()->json($courts_list);
	}
	
	
	
	
	
	
	
	
	/**
	 * @SWG\Get(
	 *     path="/courts/autocomplete",
	 *     summary="Перелік назв судових установ для поля автодоповнення",
	 *     description="Швидко отримати перелік назв судів для 'живого пошуку' Всі результати пошуку повертаються по 5 шт.",
	 *     operationId="courts-autocomplete",
	 *     produces={"application/json"},
	 *     tags={"Суди"},
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
	 *     description="Пошук за назвою суду. Повинен містити від 1 до 20 символів. Будуть повернуті назви всіх судів, початок назви яких співпадає з заданим параметром.
	НАПРИКЛАД: 'host/api/v1/courts/autocomplete?search=Дніпр' - означає, що потрібно отримати суди, назва яких починається на 'Дніпр%'",
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
	 *     	   @SWG\Property(property="court_code", type="string", description="Код суду"),
	 *     	   @SWG\Property(property="name", type="string", description="Назва суду"),
	 *     	   ),
	 *     	   examples={"application/json":
	 *              {
	 *					{
	 *     				"court_code": 2320,
 	 *					"name": "Шевченківський районний суд м. Запоріжжя"
 	 *					},
 	 *					{
	 *     				"court_code": 390,
 	 *					"name": "Шевченківський районний суд м. Львова"
 	 *					},
 	 *					{
	 *     				"court_code": 2140,
 	 *					"name": "Шевченківський районний суд Харківської області"
 	 *					},
 	 *					{
	 *     				"court_code": 210,
 	 *					"name": "Шепетівський міськрайонний суд Хмельницької області"
 	 *					},
 	 *					{
	 *     				"court_code": 350,
 	 *					"name": "Шевченківський районний суд м. Чернівців"
	 *					}
	 *				}
	 *     		}
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
		$search = Input::has('search') ? trim(Input::get('search')) : '';
		
		if (strlen($search) < 1 || !preg_match("/^[а-яєіїґ' ]+$/iu", $search)) {
			return response()->json([]);
		}
		
		// валідація фільтрів
		$request->validate([
			'search' => 'string|min:1|max:20'
		]);
		// приведення першої букви в верхній регістр для валідного пошуку
		$search = mb_convert_case($search, MB_CASE_TITLE, "UTF-8");
		$autocomplete = Court::getAutocomplete($search);
		return response()->json($autocomplete);
	}



    /**
     *
     * @SWG\Get(
     *     path="/guest/courts/{id}",
     *     summary="Дані про певний суд",
     *     description="Отримати дані про суд, id якого передано в параметрах; якщо будь-які дані будуть невідомі, значення відповідного параметра буде NULL",
     *     operationId="guest-courts-id",
     *     produces={"application/json"},
     *     tags={"Суди"},
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
     *     description="Id суду, про якого потрібно отримати дані",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     maximum=9999,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="ОК",
     *         @SWG\Schema(
     *           @SWG\Property(property="court_code", type="integer", description="Код суду"),
     *           @SWG\Property(property="name", type="string", description="Назва суду"),
     *           @SWG\Property(property="phone", type="string", description="Телефони суду"),
     *           @SWG\Property(property="email", type="string", description="Email суду"),
     *           @SWG\Property(property="site", type="string", description="Сайт суду"),
     *           @SWG\Property(property="rating", type="integer", description="Рейтинг суду"),
     *           @SWG\Property(property="instance", type="string", description="Інстанція суду"),
     *           @SWG\Property(property="region", type="string", description="Область суду"),
     *           @SWG\Property(property="jurisdiction", type="string", description="Юрисдикція"),
     *           @SWG\Property(property="head_judge", type="string", description="ПІБ головного судді"),
     *           @SWG\Property(property="address", type="array", description="Масив унікальних адрес всіх суддів з цього суду", @SWG\Items(type="string")),
     *
     *           @SWG\Property(
     *                property="judges",
     *                type="array",
     *                description="Список всіх суддів даного суду в порядку їх рейтингу від найвищого до найнижчого",
     *                @SWG\Items(
     *                    type="object",
     *     	  			  @SWG\Property(property="surname", type="string", description="Прізвище судді"),
     *                    @SWG\Property(property="name", type="string", description="Ім'я судді"),
     *                    @SWG\Property(property="patronymic", type="string", description="По-батькові судді"),
     *                    @SWG\Property(property="status", type="integer", description="Id поточного статусу судді
     *                      1 - суддя на роботі
     *                      2 - На лікарняному
     *                      3 - У відпустці
     *                      4 - Відсуній на робочому місці з інших причин
     *                      5 - Припинено повноваження"),
     *     	  			  @SWG\Property(property="updated_status", type="string", description="Дата оновлення статусу"),
     *                    @SWG\Property(property="due_date_status", type="string", description="Дата дії статусу (до якого часу даний статус буде діяти)"),
     *                    @SWG\Property(property="rating", type="integer", description="Рейтинг судді"),
     *                ),
     *              ),
     *
     *         @SWG\Property(
     *              property="court_sessions",
     *              type="array",
     *              description="Список судових засідань у даному суді розташований в хронологічному порядку",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(property="date", type="string", description="Час та дата розгляду"),
     *                  @SWG\Property(property="judges", type="string", description="Склад суду"),
     *     				@SWG\Property(property="fоrma", type="string", description="Форма судочинства [Цивільне, Кримінальне ...]"),
     *                  @SWG\Property(property="number", type="string", description="Номер справи"),
     *                  @SWG\Property(property="involved", type="string", description="Сторони у справі"),
     *                  @SWG\Property(property="description", type="string", description="Суть справи"),
     *              ),
     *            ),
     *          ),
     *         examples={"application/json":
     *              {
     *                   "court_code": 201,
     *                   "name": "Барський районний суд Вінницької області",
     *                   "phone": "(04341) 2-41-74( канцелярія)",
     *                   "email": "inbox@brs.vn.court.gov.ua",
     *                   "site": "https://brs.vn.court.gov.ua",
     *                   "rating": 0,
     *                   "instance": "Перша",
     *                   "region": "Вінницька область",
     *                   "jurisdiction": "Загальна",
     *                   "head_judge": "Єрмічова Віта Валентинівна",
     *                   "address": {
     *                       "23000, Вінницька, Барський, м. Бар, вул.Соборна, 2",
     *                       "23000, м. Бар, вул. Соборна, 2"
     *                   },
     *                   "judges": {
     *                       {
     *							 "id": 124,
     *                           "surname": "Питель",
     *                           "name": "Олена",
     *                           "patronymic": "Віталіївна",
     *                           "status": 1,
     *                           "updated_status": "2018-11-15 19:56:16",
     *                           "due_date_status": null,
     *                           "rating": 0
     *                       },
     *                       {
     *							 "id": 5124,	
     *                           "surname": "Єрмічова",
     *                           "name": "Віта",
     *                           "patronymic": "Валентинівна",
     *                           "status": 1,
     *                           "updated_status": "2018-11-15 18:35:43",
     *                           "due_date_status": "2018-06-09",
     *                           "rating": 0
     *                       }
     *                   },
     *                   "court_sessions": {
     *                       {
     *                           "date": "2018-11-02 08:00:00",
     *                           "judges": "Єрмічова Віта Валентинівна",
     *                           "forma": "Цивільне",
     *                           "number": "125/1845/18",
     *                           "involved": "Позивач: Салюк Олена Ігорівна, відповідач: Беша Олександр Вікторович",
     *                           "description": "про визнання права власності на спадкове майно за законом"
     *                       },
     *                       {
     *                           "date": "2018-11-02 09:00:00",
     *                           "judges": "Хитрук Володимир Миколайович",
     *                           "forma": "Цивільне",
     *                           "number": "125/2019/18",
     *                           "involved": "Заявник: Іздепський Василь Васильович, заінтересована особа: Кузьминецька сільська рада",
     *                           "description": "про встановлення факту, що має юридичне значення"
     *                       }
     *                   }
     *               }
     *	 		}
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, у відповіді буде зазначена причина",
     *         examples={"application/json":
     *              {
     *                  "message": "Неіснуючий id суду!"
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
	public function showGuest(int $id) {
	    return response()->json(Court::getCourtByIdGuest($id), 200);
    }

    /**
     * @SWG\Get(
     *     path="/courts/{id}",
     *     summary="Дані про певний суд",
     *     description="Отримати дані про суд, id якого передано в параметрах; якщо будь-які дані будуть невідомі, значення відповідного параметра буде NULL",
     *     operationId="courts-id",
     *     produces={"application/json"},
     *     tags={"Суди"},
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
     *     description="Id суду, про якого потрібно отримати дані",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
     *     maximum=9999,
     *     allowEmptyValue=false
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="ОК",
     *         @SWG\Schema(
     *           @SWG\Property(property="court_code", type="integer", description="Код суду"),
     *           @SWG\Property(property="name", type="string", description="Назва суду"),
     *           @SWG\Property(property="phone", type="string", description="Телефони суду"),
     *           @SWG\Property(property="email", type="string", description="Email суду"),
     *           @SWG\Property(property="site", type="string", description="Сайт суду"),
     *           @SWG\Property(property="rating", type="integer", description="Рейтинг суду"),
     *           @SWG\Property(property="instance", type="string", description="Інстанція суду"),
     *           @SWG\Property(property="region", type="string", description="Область суду"),
     *           @SWG\Property(property="jurisdiction", type="string", description="Юрисдикція"),
     *           @SWG\Property(property="head_judge", type="string", description="ПІБ головного судді"),
     *           @SWG\Property(property="is_bookmark", type="integer", description="Чи є суд в закладках поточного користувача (1 або 0)"),
     *           @SWG\Property(property="address", type="array", description="Масив унікальних адрес всіх суддів з цього суду", @SWG\Items(type="string")),
     *
     *           @SWG\Property(
     *                property="judges",
     *                type="array",
     *                description="Список всіх суддів даного суду в порядку їх рейтингу від найвищого до найнижчого",
     *                @SWG\Items(
     *                    type="object",
     *     	  			  @SWG\Property(property="surname", type="string", description="Прізвище судді"),
     *                    @SWG\Property(property="name", type="string", description="Ім'я судді"),
     *                    @SWG\Property(property="patronymic", type="string", description="По-батькові судді"),
     *                    @SWG\Property(property="status", type="integer", description="Id поточного статусу судді
     *                      1 - суддя на роботі
     *                      2 - На лікарняному
     *                      3 - У відпустці
     *                      4 - Відсуній на робочому місці з інших причин
     *                      5 - Припинено повноваження"),
     *     	  			  @SWG\Property(property="updated_status", type="string", description="Дата оновлення статусу"),
     *                    @SWG\Property(property="due_date_status", type="string", description="Дата дії статусу (до якого часу даний статус буде діяти)"),
     *                    @SWG\Property(property="rating", type="integer", description="Рейтинг судді"),
     *                    @SWG\Property(property="is_bookmark", type="integer", description="Чи є суддя в закладках поточного користувача (1 або 0)"),
     *                ),
     *              ),
     *
     *         @SWG\Property(
     *              property="court_sessions",
     *              type="array",
     *              description="Список судових засідань у даному суді розташований в хронологічному порядку",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(property="date", type="string", description="Час та дата розгляду"),
     *                  @SWG\Property(property="judges", type="string", description="Склад суду"),
     *     				@SWG\Property(property="fоrma", type="string", description="Форма судочинства [Цивільне, Кримінальне ...]"),
     *                  @SWG\Property(property="number", type="string", description="Номер справи"),
     *                  @SWG\Property(property="involved", type="string", description="Сторони у справі"),
     *                  @SWG\Property(property="description", type="string", description="Суть справи"),
     *                  @SWG\Property(property="is_bookmark", type="integer", description="Чи є засідання в закладках поточного користувача (1 або 0)"),
     *              ),
     *            ),
     *          ),
     *         examples={"application/json":
     *              {
     *                   "court_code": 201,
     *                   "name": "Барський районний суд Вінницької області",
     *                   "phone": "(04341) 2-41-74( канцелярія)",
     *                   "email": "inbox@brs.vn.court.gov.ua",
     *                   "site": "https://brs.vn.court.gov.ua",
     *                   "rating": 0,
     *                   "instance": "Перша",
     *                   "region": "Вінницька область",
     *                   "jurisdiction": "Загальна",
     *                   "head_judge": "Єрмічова Віта Валентинівна",
     *                   "is_bookmark": 0,
     *                   "address": {
     *                       "23000, Вінницька, Барський, м. Бар, вул.Соборна, 2",
     *                       "23000, м. Бар, вул. Соборна, 2"
     *                   },
     *                   "judges": {
     *                       {
     *							 "id": 124,	
     *                           "surname": "Питель",
     *                           "name": "Олена",
     *                           "patronymic": "Віталіївна",
     *                           "status": 1,
     *                           "updated_status": "2018-11-15 19:56:16",
     *                           "due_date_status": null,
     *                           "rating": 0,
     *                           "is_bookmark": 1
     *                       },
     *                       {
     *							 "id": 5124,	
     *                           "surname": "Єрмічова",
     *                           "name": "Віта",
     *                           "patronymic": "Валентинівна",
     *                           "status": 1,
     *                           "updated_status": "2018-11-15 18:35:43",
     *                           "due_date_status": "2018-06-09",
     *                           "rating": 0,
     *                           "is_bookmark": 0
     *                       }
     *                   },
     *                   "court_sessions": {
     *                       {
     *                           "date": "2018-11-02 08:00:00",
     *                           "judges": "Єрмічова Віта Валентинівна",
     *                           "forma": "Цивільне",
     *                           "number": "125/1845/18",
     *                           "involved": "Позивач: Салюк Олена Ігорівна, відповідач: Беша Олександр Вікторович",
     *                           "description": "про визнання права власності на спадкове майно за законом",
     *                           "is_bookmark": 0
     *                       },
     *                       {
     *                           "date": "2018-11-02 09:00:00",
     *                           "judges": "Хитрук Володимир Миколайович",
     *                           "forma": "Цивільне",
     *                           "number": "125/2019/18",
     *                           "involved": "Заявник: Іздепський Василь Васильович, заінтересована особа: Кузьминецька сільська рада",
     *                           "description": "про встановлення факту, що має юридичне значення",
     *                           "is_bookmark": 1
     *                       }
     *                   }
     *               }
     *	 		}
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані, у відповіді буде зазначена причина",
     *         examples={"application/json":
     *              {
     *                  "message": "Неіснуючий id суду!"
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
    public function show(int $id) {
        return response()->json(Court::getCourtById($id), 200);
    }
	
	
	
	/**
	 * @SWG\Put(
	 *     path="/courts/{id}/bookmark",
	 *     summary="Додати суд в закладки",
	 *     description="Додати суд, в закладки поточного користувача",
	 *     operationId="courts-addBookmark",
	 *     produces={"application/json"},
	 *     tags={"Суди"},
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
	 *     description="Id суду, який потрібно додати в закладки поточного користувача",
	 *     type="integer",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     minimum=100,
	 *     maximum=10000,
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
	public function addCourtBookmark($id) {
		$id = intval($id);
		
		if (UserBookmarkCourt::checkBookmark(Auth::user()->id, $id)) {
			return response()->json([
				'message' => 'Закладка вже існує'
			], 422);
		}
		UserBookmarkCourt::createBookmark(Auth::user()->id, $id);
		return response()->json([
			'message' => 'Закладка успішно створена'
		], 201);
	}
	
	
	
	/**
	 * @SWG\Delete(
	 *     path="/courts/{id}/bookmark",
	 *     summary="Видалити суд з закладок",
	 *     description="Видалити суд, з закладок поточного користувача",
	 *     operationId="courts-delBookmark",
	 *     produces={"application/json"},
	 *     tags={"Суди"},
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
	 *     description="Id суду, який потрібно видалити з закладок поточного користувача",
	 *     type="integer",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     minimum=100,
	 *     maximum=10000,
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
	public function delCourtBookmark($id) {
		$id = intval($id);
		
		if (!UserBookmarkCourt::checkBookmark(Auth::user()->id, $id)) {
			return response()->json([
				'message' => 'Закладки не існує'
			], 422);
		}
		UserBookmarkCourt::deleteBookmark(Auth::user()->id, $id);
		return response()->json([], 204);
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
			'search'=>$search]);
	}
}
