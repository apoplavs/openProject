<?php

namespace Toecyd\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Toecyd\Http\Controllers\Controller;
use Toecyd\UserBookmarkJudge;

/**
 * Class UserController
 * @package Toecyd\Http\Controllers\Api\V1
 * відповідає за всі активні дії користувача з його акаунтом
 */
class UserController extends Controller
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
	 * Get the authenticated User
	 *
	 * @SWG\Get(
	 *     path="/user",
	 *     summary="Отримати дані користувача",
	 *     description="Отримати дані про поточного користувача",
	 *     operationId="user",
	 *     produces={"application/json"},
	 *     tags={"Автентифікація користувача"},
	 *     security={
	 *     {"passport": {}},
	 *    },
	 *     @SWG\Parameter(
	 *        ref="#/parameters/Content-Type",
	 *     ),
	 *     @SWG\Parameter(
	 *        ref="#/parameters/X-Requested-With",
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=200,
	 *         description="ОК",
	 *        @SWG\Schema(ref="#/definitions/User"),
	 *           examples={"application/json":
	 *              {
	 *                    "name": "Іван",
	 *                    "surname": null,
	 *                    "phone": null,
	 *                    "email": "example@mail.com",
	 *                    "photo": null,
	 *                    "usertype": 2,
	 *                    "created_at": "2018-08-01 15:26:05",
	 *                    "updated_at": "2018-08-01 15:26:05"
	 *                }
	 *            }
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Необхідна аутентифікація користувача, можливо токен не існує, або анульований",
	 *           examples={"application/json":
	 *              {
	 *                    "message": "Unauthenticated",
	 *              }
	 *            }
	 *     ),
	 *     @SWG\Response(
	 *         response=405,
	 *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом POST, хоча очікується GET.",
	 *     )
	 * )
	 *
	 * @return [json] user object
	 */
	public function show(Request $request) {
		return response()->json($request->user());
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
    public function destroy(Request $request)
    {
		$request->user()->token()->revoke();
    }
}
