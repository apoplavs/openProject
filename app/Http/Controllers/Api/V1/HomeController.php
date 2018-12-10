<?php

namespace Toecyd\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Toecyd\Http\Controllers\Controller;
use Toecyd\UserBookmarkCourt;
use Toecyd\UserBookmarkJudge;
use Toecyd\UserBookmarkSession;
use Toecyd\UserHistory;

/**
 * Class HomeController
 * @package Toecyd\Http\Controllers\Api\V1
 */
class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
	 * Get the User the history of user
	 *
	 * @SWG\Get(
	 *     path="/user/history",
	 *     summary="Отримати історію переглядів",
	 *     description="Отримати історію переглядів поточного користувача",
	 *     operationId="user-history",
	 *     produces={"application/json"},
	 *     tags={"Особистий кабінет"},
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
	 *     @SWG\Response(
	 *         response=200,
	 *         description="ОК",
	 *        @SWG\Schema(
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
	 *     	   @SWG\Property(property="is_bookmark", type="integer", description="Чи знаходиться в закладках в поточного користувача 1 - так,  0 - ні")
	 *     	   ),
	 *     	   examples={"application/json":
	 *         {
	 *				{
	 *						"id": 7514,
	 *						"court_name": "Овруцький районний суд Житомирської області",
	 *						"surname": "Чичирко",
	 *						"name": "В",
	 *						"patronymic": "А",
	 *						"photo": "/img/judges/no_photo.jpg",
	 *						"status": 1,
	 *						"updated_status": "23.5.2018",
	 *						"due_date_status": null,
	 *						"rating": 0,
	 *						"is_bookmark": 1
	 *						},
	 *						{
	 *						"id": 5650,
	 *						"court_name": "Апеляційний суд Чернігівської області",
	 *						"surname": "Шарапова",
	 *						"name": "Олена",
	 *						"patronymic": "Леонідівна",
	 *						"photo": "/img/judges/no_photo.jpg",
	 *						"status": 1,
	 *						"updated_status": "23.5.2018",
	 *						"due_date_status": null,
	 *						"rating": 0,
	 *						"is_bookmark": 0
	 *						},
	 *						{
	 *						"id": 1078,
	 *						"court_name": "Господарський суд Одеської області",
	 *						"surname": "Шаратов",
	 *						"name": "Юрій",
	 *						"patronymic": "Анатолійович",
	 *						"photo": "/img/judges/no_photo.jpg",
	 *						"status": 1,
	 *						"updated_status": "21.7.2018",
	 *						"due_date_status": null,
	 *						"rating": 0,
	 *						"is_bookmark": 0
	 *						},
	 *						{
	 *						"id": 9064,
	 *						"court_name": "Вищий господарський суд України",
	 *						"surname": "Шаргало",
	 *						"name": "В",
	 *						"patronymic": "І",
	 *						"photo": "/img/judges/no_photo.jpg",
	 *						"status": 1,
	 *						"updated_status": "23.5.2018",
	 *						"due_date_status": null,
	 *						"rating": 0,
	 *						"is_bookmark": 0
	 *						},
	 *						{
	 *						"id": 4526,
	 *						 "court_name": "Господарський суд Запорізької області",
	 *						 "surname": "Шевченко",
	 *						"name": "Т",
	 *						"patronymic": "М",
	 *						"photo": "/img/judges/no_photo.jpg",
	 *						"status": 1,
	 *						"updated_status": "23.5.2018",
	 *						"due_date_status": null,
	 *						"rating": 0,
	 *						"is_bookmark": 0
	 *						}
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
	 *     )
	 * )
	 *
	 * @return [json] user object
	 */
	public function indexHistory() {
		$judges_history = UserHistory::getHistoryJudges();
		return response()->json($judges_history);
	}

    /**
     * Get the bookmarks of user
     *
     * @SWG\Get(
     *     path="/user/bookmarks",
     *     summary="Отримати закладки",
     *     description="Отримати закладки поточного користувача",
     *     operationId="user-bookmarks",
     *     produces={"application/json"},
     *     tags={"Особистий кабінет"},
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
     *     @SWG\Response(
     *         response=200,
     *         description="ОК",
     *        @SWG\Schema(
     *          @SWG\Property(
     *              property="courts",
     *              description="Список судових установ, що знаходяться в закладках у користувача",
     *              type="array",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(property="court_code", type="string", description="Код суду"),
     *                  @SWG\Property(property="court_name", type="string", description="Назва суду"),
     *     	            @SWG\Property(property="instance", type="string", description="Інстанція суду [Перша, Апеляційна, Касаційна]"),
     *     	            @SWG\Property(property="region", type="string", description="Регіон суду"),
     *     	            @SWG\Property(property="jurisdiction", type="string", description="Юрисдикція суду [Загальна, Адміністративна, Господарська]"),
     *     	            @SWG\Property(property="address", type="string", description="Адреса суду"),
     *     	            @SWG\Property(property="head_judge", type="string", description="ПІБ головуючого судді  || null якщо невідомо"),
     *     	            @SWG\Property(property="rating", type="integer", description="Місце в рейтингу серед усіх судових установ"),
     *              )
     *          ),
     *         @SWG\Property(
     *              property="judges",
     *              type="array",
     *              description="Список суддів, що знаходяться в закладках у користувача",
     *              @SWG\Items(
     *                  type="object",
     *     	            @SWG\Property(property="id", type="string", description="id судді"),
     *     	            @SWG\Property(property="court_name", type="string", description="Назва суду, в якому даний суддя працює"),
     *     	            @SWG\Property(property="surname", type="string", description="Прізвище судді"),
     *     	            @SWG\Property(property="name", type="string", description="Ім'я судді"),
     *     	            @SWG\Property(property="patronymic", type="string", description="По батькові судді"),
     *     	            @SWG\Property(property="photo", type="string", description="URL фото судді"),
     *     	            @SWG\Property(property="status", type="integer", description="Id поточного статусу судді
     * 	                    1 - суддя на роботі
     *	                    2 - На лікарняному
     *	                    3 - У відпустці
     *	                    4 - Відсуній на робочому місці з інших причин
     *	                    5 - Припинено повноваження"),
     *     	            @SWG\Property(property="updated_status", type="string", description="Дата останнього оновлення статусу"),
     *     	            @SWG\Property(property="due_date_status", type="string", description="Дата дії статусу (до якого часу даний статус буде діяти)"),
     *     	            @SWG\Property(property="rating", type="integer", description="Місце в рейтингу серед усіх суддів"),
     *              ),
     *            ),
     *          @SWG\Property(
     *              property="court-sessions",
     *              type="array",
     *              description="Список судових засідань, що знаходяться в закладках у користувача",
     *              @SWG\Items(
     *                  type="object",
     *     	            @SWG\Property(property="id", type="string", description="id судового засідання"),
     *     	            @SWG\Property(property="date", type="string", description="Дата судового засідання"),
     *     	            @SWG\Property(property="judges", type="string", description="Склад суду"),
     *     	            @SWG\Property(property="court_code", type="string", description="Номер суду"),
     *     	            @SWG\Property(property="name", type="string", description="Назва суду"),
     *     	            @SWG\Property(property="number", type="string", description="Номер справи"),
     *     	            @SWG\Property(property="involved", type="string", description="Учасники справи"),
     *     	            @SWG\Property(property="description", type="string", description="Короткий опис справи"),
     *              ),
     *            ),
     *          ),
     *           examples={"application/json":
     *     	         {
     *                  "judges":{
     *                      {
     *                          "id":302,
     *                          "court_name":"Барський районний суд Вінницької області",
     *                          "surname":"Хитрук",
     *                          "name":"Володимир",
     *                          "patronymic":"Миколайович",
     *                          "photo":"/img/judges/no_photo.jpg",
     *                          "status":1,
     *                          "updated_status":"23.05.2018",
     *                          "due_date_status":null,
     *                          "rating":0
     *                      },
     *                      {
     *                          "id":3312,
     *                          "court_name":"Барський районний суд Вінницької області",
     *                          "surname":"Переверзєв",
     *                          "name":"Сергій",
     *                          "patronymic":"Володимирович",
     *                          "photo":"/img/judges/no_photo.jpg",
     *                          "status":1,
     *                          "updated_status":"03.10.2018",
     *                          "due_date_status":null,
     *                          "rating":0
     *                      }
     *                  },
     *                  "courts":{
     *                      {
     *                          "court_code":106,
     *                          "court_name":"Євпаторійський міський суд Автономної Республіки Крим",
     *                          "instance":"Перша",
     *                          "region":"Автономна Республіка Крим",
     *                          "jurisdiction":"Загальна",
     *                          "address":"97412, м. Євпаторія, пр. Леніна, 30",
     *                          "head_judge":null,
     *                          "rating":0
     *                      },
     *                      {
     *                          "court_code":1410,
     *                          "court_name":"Єланецький районний суд Миколаївської області",
     *                          "instance":"Перша",
     *                          "region":"Миколаївська область",
     *                          "jurisdiction":"Загальна",
     *                          "address":"55501, смт Єланець, вул. Аграрна, 16",
     *                          "head_judge":null,
     *                          "rating":0
     *                      }
     *                  },
     *                  "court-sessions":{
     *                      {
     *                          "id":2,
     *                          "date":"2018-11-02 08:15:00",
     *                          "judges":"головуючий суддя: Воробйов Андрій Володимирович; учасник колегії: Ходько В М; учасник колегії: Наполов Микола Іванович",
     *                          "court_code":"201",
     *                          "name":"Барський районний суд Вінницької області",
     *                          "number":"125/1530/18",
     *                          "involved":"Позивач: АТ КБ БАНК, відповідач: Шевченко Іван Іваноич",
     *                          "description":"про стягнення заборгованості"
     *                      },
     *                      {
     *                          "id":3,
     *                          "date":"2018-11-03 08:15:00",
     *                          "judges":"Єрмічова Віта Валентинівна",
     *                          "court_code":"201",
     *                          "name":"Барський районний суд Вінницької області",
     *                          "number":"125/1530/19",
     *                          "involved":"",
     *                          "description":""
     *                      }
     *                  }
     *              }
     *          }
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
     *     )
     * )
     *
     * @return [json] user object
     */
	public function indexBookmarks() {
		return response()->json([
		    'judges'            => UserBookmarkJudge::getBookmarkJudges(),
            'courts'            => UserBookmarkCourt::getBookmarkCourts(),
            'court-sessions'    => UserBookmarkSession::getBookmarks(),
        ]);
	}

}
