<?php

namespace Toecyd\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Toecyd\Http\Controllers\Controller;
use Toecyd\UserBookmarkSession;

/**
 * Class CourtSessionController
 * @package Toecyd\Http\Controllers\Api\V1
 */
class CourtSessionController extends Controller
{

    /**
     * Get the bookmarks of user
     *
     * @SWG\Get(
     *     path="/court-sessions/bookmarks",
     *     summary="Отримати список закладок на судові засідання",
     *     description="Отримати список судових засідань, що знаходяться в закладках поточного користувача",
     *     operationId="court-sessions-bookmarks",
     *     produces={"application/json"},
     *     tags={"Судові засідання"},
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
     *     @SWG\Response(
     *         response=200,
     *         description="ОК",
     *        @SWG\Schema(
     *          @SWG\Property(
     *              property="court-sessions",
     *              type="array",
     *              description="Список судових засідань, що знаходяться в закладках у користувача",
     *              @SWG\Items(
     *                  type="object",
     *                  @SWG\Property(property="id", type="string", description="id судового засідання"),
     *                  @SWG\Property(property="date", type="string", description="Дата судового засідання"),
     *                  @SWG\Property(property="judges", type="string", description="Склад суду"),
     *                  @SWG\Property(property="court_code", type="string", description="Номер суду"),
     *                  @SWG\Property(property="name", type="string", description="Назва суду"),
     *                  @SWG\Property(property="forma", type="string", description="Форма судочинства"),
     *                  @SWG\Property(property="number", type="string", description="Номер справи"),
     *                  @SWG\Property(property="involved", type="string", description="Учасники справи"),
     *                  @SWG\Property(property="description", type="string", description="Короткий опис справи"),
     *              ),
     *            ),
     *          ),
     *           examples={"application/json":
     *               {
 *                      {
 *                          "id":2,
 *                          "date":"2018-11-02 08:15:00",
 *                          "judges":"головуючий суддя: Воробйов Андрій Володимирович; учасник колегії: Ходько В М; учасник колегії: Наполов Микола Іванович",
 *                          "court_code":"201",
 *                          "name":"Барський районний суд Вінницької області",
 *                          "forma":"Цивільне",
 *                          "number":"125/1530/18",
 *                          "involved":"Позивач: АТ КБ БАНК, відповідач: Шевченко Іван Іваноич",
 *                          "description":"про стягнення заборгованості",
 *     						"note": "підготувати документи"
 *                      },
 *                      {
 *                          "id":3,
 *                          "date":"2018-11-03 08:15:00",
 *                          "judges":"Єрмічова Віта Валентинівна",
 *                          "court_code":"201",
 *                          "name":"Барський районний суд Вінницької області",
 *                          "forma":"Адмінправопорушення",
 *                          "number":"125/1530/19",
 *                          "involved":"",
 *                          "description":"",
 *     						"note": null
 *                      }
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
     *     )
     * )
     *
     * @return [json] user object
     */
    public function index() {
        return response()->json(UserBookmarkSession::getBookmarks());
    }
    

    /**
     * @SWG\Put(
     *     path="/court-sessions/{id}/bookmark",
     *     summary="Додати судове засідання в закладки",
     *     description="Додати судове засідання, в закладки поточного користувача",
     *     operationId="courtSessions-addBookmark",
     *     produces={"application/json"},
     *     tags={"Судові засідання"},
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
     *     description="Id судового засідання, якого потрібно додати в закладки поточного користувача",
     *     type="integer",
     *     collectionFormat="multi",
     *     uniqueItems=true,
     *     minimum=1,
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
	public function addSessionBookmark($court_session_id) {
	    $user_id = Auth::user()->id;
        $court_session_id = intval($court_session_id);

        if (UserBookmarkSession::checkBookmark($user_id, $court_session_id)) {
            return response()->json(['message' => 'Закладка вже існує'], 422);
        }

        UserBookmarkSession::createBookmark($user_id, $court_session_id);
        return response()->json(['message' => 'Закладка успішно створена'], 201);
    }

    /**
     * @SWG\Delete(
     *     path="/court-sessions/{id}/bookmark",
     *     summary="Видалити судове засідання з закладок",
     *     description="Видалити судове засідання, з закладок поточного користувача",
     *     operationId="courtSessions-delBookmark",
     *     produces={"application/json"},
     *     tags={"Судові засідання"},
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
     *     description="Id судового засідання, якого потрібно видалити з закладок поточного користувача",
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
    public function deleteSessionBookmark($court_session_id) {
	    $user_id = Auth::user()->id;
	    $court_session_id = intval($court_session_id);

        if (!UserBookmarkSession::checkBookmark($user_id, $court_session_id)) {
            return response()->json(['message' => 'Закладки не існує'], 422);
        }

        UserBookmarkSession::deleteBookmark($user_id, $court_session_id);
        return response()->json([], 204);
    }
	
	
	/**
	 * @SWG\Post(
	 *     path="/court-sessions/{id}/bookmark/note",
	 *     summary="Створити примітку до закладки на судове засідання",
	 *     description="Створити примітку до закладки на судове засідання поточного користувача",
	 *     operationId="courtSessions-bookmark-note",
	 *     produces={"application/json"},
	 *     tags={"Судові засідання"},
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
	 *     description="Id судового засідання, на яке потрібно створити примітку. Дане судове засідання повинно знаходитись в закалдках  користувача",
	 *     type="integer",
	 *     collectionFormat="multi",
	 *     uniqueItems=true,
	 *     minimum=1,
	 *     allowEmptyValue=false
	 *     ),
	 *
	 *     @SWG\Parameter(
	 *     name="Текст замітки",
	 *     required=true,
	 *     in="body",
	 *     description="Текст замітки, яка буде записана до поточної закладки на судове засідання. Максимальна довжина 255 символів",
	 *     @SWG\Schema(
	 *          type="object",
	 *            required={"note"},
	 *          @SWG\Property(property="note", type="string", example="підготувати документи", description="Текст замітки"),
	 *       )
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Примітка упішно створена",
	 *         examples={"application/json":
	 *              {
	 *                  "message": "Примітка успішно створена/оновлена"
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
	 *         response=403,
	 *         description="Користувач немає доступу до закладки на дане судове засідання,
	 * можливо дане судове засідання не знаходиться в закалдках поточного користувача",
	 *         examples={"application/json":
	 *              {
	 *                  "message": "Заборонено!",
	 *              }
	 *          }
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=405,
	 *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом PUT, хоча очікується POST.",
	 *     ),
	 * )
	 */
	public function addNote($court_session_id, Request $request) {
		
		$request->validate([
			'note'    => 'required|string|max:255'
		]);
		
		$court_session_id = intval($court_session_id);
		
		UserBookmarkSession::writeNoteForBookmark($court_session_id, $request->note);
		return response()->json(['message' => 'Примітка успішно створена/оновлена'], 200);
	}
	
}
