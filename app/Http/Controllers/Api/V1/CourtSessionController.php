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
	 *     path="/court-sessions/bookmark/{id}/note",
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
	 *     description="Id закладки на судове засідання, для якої потрібно створити примітку",
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
	 *         response=201,
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
	 *         response=405,
	 *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом PUT, хоча очікується POST.",
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=422,
	 *         description="Передані не валідні дані, неіснуючий id закладки",
	 *         examples={"application/json":
	 *              {
	 *                  "message": "Неіснуючий id",
	 *              }
	 *          }
	 *     ),
	 * )
	 */
	public function addNote($bookmark_id, Request $request) {
		
		$request->validate([
			'note'    => 'required|string|max:255'
		]);
		
		// todo зробити перевірку прав користувача додавати замітку до даної закладки
		$user_id = Auth::user()->id;
		
		$bookmark_id = intval($bookmark_id);
		
		UserBookmarkSession::writeNoteForBookmark($bookmark_id, $request->note);
		return response()->json(['message' => 'Примітка успішно створена/оновлена'], 201);
	}
	
}
