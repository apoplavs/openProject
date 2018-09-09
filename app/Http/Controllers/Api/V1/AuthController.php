<?php
/**
 * Created by PhpStorm.
 * User: apoplavs
 * Date: 01.08.18
 * Time: 17:04
 */

namespace Toecyd\Http\Controllers\Api\V1;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Toecyd\Http\Controllers\Controller;
use Carbon\Carbon;
use Toecyd\User;

/**
 * Class AuthController
 * відповідає за аутенфікацію коритувачів
 * @package Toecyd\Http\Controllers\Api\V1\Auth
 */
class AuthController extends Controller
{
	/**
	 * Реєстрація
	 * @SWG\Post(
	 *     path="/signup",
	 *     summary="Реєстрація",
	 *     description="Зареєструвати нового користувача",
	 *     operationId="signup",
	 *     produces={"application/json"},
	 *     tags={"Автентифікація користувача"},
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/Content-Type",
	 *     ),
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/X-Requested-With",
	 *     ),
	 *
	 *     @SWG\Parameter(
	 *     name="Дані користувача",
	 *     in="body",
	 *     description="Щоб зареєструвати нового користувача в системі, потрібно надіслати його ім'я, email і пароль. Питання про підтвердження паролю користувачем залишається на Вашій стороні.",
	 *     @SWG\Schema(
	 *          type="object",
	 *     		required={"name", "email", "password"},
	 *          @SWG\Property(property="name", type="string", example="NameOfUser", description="Ім'я Користувача (повинно складатися від 3 до 255 символів)"),
	 *          @SWG\Property(property="email", type="string", example="example@gmail.com", description="Email Користувача (існуючий, валідний, унікальний в системі)"),
	 *          @SWG\Property(property="password", type="string", example="123456", description="Пароль Користувача (повинен містити від 6 до 32 символів)")
	 *       )
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=201,
	 *         description="Користувач успішно зареєстрований",
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "Successfully created user!"
	 *              }
	 *     		}
	 *     ),
	 *     @SWG\Response(
	 *         response=422,
	 *         description="Передані не валідні дані, або їх не достатньо, у відповіді буде зазначено у чому причина",
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "The given data was invalid.",
	 *                  "errors": {
	 *                  	"email": {
	 *     							"Даний email вже використовується."
	 * 						}
	 *     				}
	 *              }
	 *     		}
	 *     ),
	 *     @SWG\Response(
	 *         response=405,
	 *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом GET, хоча очікується що дані будуть надіслані методом POST.",
	 *     )
	 * )
	 *
	 * @param  [string] name
	 * @param  [string] email
	 * @param  [string] password
	 * @return [string] message
	 */
	public function signup(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255|min:3',
			'email' => 'required|string|email|unique:users|max:255',
			'password' => 'required|string|min:6|max:32'
		]);
		$user = new User([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password)
		]);
		$user->save();
		return response()->json([
			'message' => 'Successfully created user!'
		], 201);
	}
	
	
	public function test(Request $request)
	{
		return (response()->json($request->all()));
		$request->validate([
			'name' => 'required|string|max:255|min:3',
			'email' => 'required|string|email|unique:users|max:255',
			'password' => 'required|string|min:6|max:32'
		]);
		$user = new User([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password)
		]);
		$user->save();
		return response()->json([
			'message' => 'Successfully created user!'
		], 201);
	}
	
	
	
	
	/**
	 * Login user and create token
	 *
	 * @SWG\Post(
	 *     path="/login",
	 *     summary="Вхід в систему",
	 *     description="Вхід користувача в систему можливий після підтвердження email;
	 Після надсилання валідного запиту, в системі генерується унікальний токен для кожного користувача;
	 Токен, потрібен для підтвердження автентифікації користувача в системі;
	 Після отримання токена користувач може здійснювати запити на маршрути шо вимагають авторизації.",
	 *     operationId="login",
	 *     produces={"application/json"},
	 *     tags={"Автентифікація користувача"},
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/Content-Type",
	 *     ),
	 *     @SWG\Parameter(
	 *     	ref="#/parameters/X-Requested-With",
	 *     ),
	 *
	 *     @SWG\Parameter(
	 *     name="Дані користувача",
	 *     in="body",
	 *     description="Щоб отримати унікальний токен, потрібно передати email і пароль користувача.",
	 *     @SWG\Schema(
	 *          type="object",
	 *     		required={"email", "password"},
	 *          @SWG\Property(property="email",  type="string", example="example@gmail.com", description="email Користувача"),
	 *          @SWG\Property(property="password", type="string", example="123456", description="Пароль Користувача"),
	 *     		@SWG\Property(property="remember_me", type="integer", example="1", enum={"1", "2", "3"}, description="За замовчуванням токен видається на 24 години, якщо неохідно отримати токен на довший час, то потрібно передати цей параметр як додатковий, з вкзазанням ідентифікатора на скільки часу потрібен токен, може приймати значення від 1 до 3, що відповідає:
	 * 	1 - два тижні;
	 *	2 - шість місяців;
	 *	3 - три роки.
 Одночасно для одного користувача може бути видано до 10 токенів, при видачі кожного насупного - попередній 10-й анулююється")
	 *       )
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Токен успішно згенерований",
	 *     	   @SWG\Schema(ref="#/definitions/Token"),
	 *     	   examples={"application/json":
	 *              {
	 *     				"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjYwODMxYTExMzQwODJmMmJlN2YyYjRhYzc4NjZkN2EzZDI5YjQ5YmQ0MWI1ZjVmNjhlMDAxZDc1NGQ5ZjlhZjFlMGFhNzcxMzM1Yjk2YzU5In0.eyJhdWQiOiIxIiwianRpIjoiNjA4MzFhMTEzNDA4MmYyYmU3ZjJiNGFjNzg2NmQ3YTNkMjliNDliZDQxYjVmNWY2OGUwMDFkNzU0ZDlmOWFmMWUwYWE3NzEzMzViOTZjNTkiLCJpYXQiOjE1MzMyMjExMTksIm5iZiI6MTUzMzIyMTExOSwiZXhwIjoxNTY0NzU3MTE5LCJzdWIiOiI0Iiwic2NvcGVzIjpbXX0.lGr3pzgbh7GZVmfyFcuHEo_gDeHbRmRoh8_UzmmL6PH_X0HgufZSg5jQH9LmBpC5p_FSGJ2bzRAWHg65CbIW0GBbh0bGHwZbBfJ2UF2n8adUfgaJbbxmKqCQmZzFkHaMs_bWG2bDP0RGYvuuqx7UgLVT_lMOjzs5bcNfK8BBY3h_1lQ0EroQJp6cRm67f6UN9GclALMQJvK5Az-jlqjWqpv61bayKoOTdITSvI4Wa6h3VKWACkVRn81oLdnDyAQ6gygM9bEJlYRKBEhPfOL23T_Jvc0tjyfe13VTpGy9FlEb5MnBUH8NfZSEZJfCBgwKrmDFnr6TFGKWaTCZRdgBEhOvv5wrh0hqW45ZiRGKqEvzQ25tgq5UYUaAwKA4vMAZg1QjdSaMkA5G7cveCXQV0o_vfF77T2sxKjA0UFNnjwCCIipuz7xMKtf0aKK53B_vccAuCPgIIR4ACzPo6YEyCpKNYfGVvSTlWN9YkMrgAaX6DBKF91r2zUisA5-xCPKHXnlkUwZX-QqlYPPkeIFDXa-9AqdqT243oLvqIq4ieOrlr4II8w96XJPHZk1PKTaXJQlVT3t3lJOY2qmrMZOL80I9RijoCHIaAWDD7jeaTaN8fcgR8sI1LIVd5N5bl4UM03nm4CbKon8P16vs22swG4zfWSHWenOLiJbqGVxsEQs",
	 *     				"token_type": "Bearer",
	 *     				"expires_at": "2019-08-02 14:45:19"
	 * 				}
	 *     		}
	 *     ),
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Передані не валідні дані (неправильний email або пароль)",
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "Unauthorized",
	 *              }
	 *     		}
	 *     ),
	 *     @SWG\Response(
	 *         response=405,
	 *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом GET, хоча очікується що дані будуть надіслані методом POST.",
	 *     ),
	 *     @SWG\Response(
	 *         response=422,
	 *         description="Запит не вдалося виконати через семантичні помилки (можливо передані не всі обов'язкові дані, або синтаксична помилка в запиті)",
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "The given data was invalid.",
	 *					"errors": {
	 *						"email": {
	 *						"email є обовязковим."
	 *						},
	 *						"password": {
	 *						"password є обовязковим."
	 *						}
	 *					}
	 *              }
	 *     		}
	 *     ),
	 * )
	 *
	 * @param  [string] email
	 * @param  [string] password
	 * @param  [boolean] remember_me
	 * @return [string] access_token
	 * @return [string] token_type
	 * @return [string] expires_at
	 */
	public function login(Request $request)
	{
		$request->validate([
			'email' => 'required|string|email',
			'password' => 'required|string',
			'remember_me' => 'int|min:1|max:3',
		]);
		$credentials = request(['email', 'password']);
		if(!Auth::attempt($credentials))
			return response()->json([
				'message' => 'Unauthorized'
			], 401);
		$user = $request->user();
		$tokenResult = $user->createToken('Personal Access Token');
		$token = $tokenResult->token;
		if (!$request->remember_me) {
			$token->expires_at = Carbon::now('Europe/Kiev')->addDay();
		} else if ($request->remember_me == 1){
			$token->expires_at = Carbon::now('Europe/Kiev')->addWeeks(2);
		} else if ($request->remember_me == 2){
			$token->expires_at = Carbon::now('Europe/Kiev')->addMonths(6);
		} else if ($request->remember_me == 3){
			$token->expires_at = Carbon::now('Europe/Kiev')->addYears(3);
		}
		
		$token->save();
		return response()->json([
			'access_token' => $tokenResult->accessToken,
			'token_type' => 'Bearer',
			'expires_at' => Carbon::parse(
				$tokenResult->token->expires_at
			)->toDateTimeString()
		]);
	}
	
	/**
	 * Logout user (Revoke the token)
	 *
	 * @SWG\Get(
	 *     path="/logout",
	 *     summary="Вихід з системи",
	 *     description="Вийти з системи і анулювати поточний токен",
	 *     operationId="logout",
	 *     produces={"application/json"},
	 *     tags={"Автентифікація користувача"},
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
	 *         description="Токен успішно анульовано",
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "Successfully logged out!"
	 *              }
	 *     		}
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Необхідна аутентифікація користувача, можливо даний токен вже анульовано",
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
	 * @return [string] message
	 */
	public function logout(Request $request)
	{
		$request->user()->token()->revoke();
		return response()->json([
			'message' => 'Successfully logged out'
		]);
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
	 *        @SWG\Schema(ref="#/definitions/User"),
	 *     	   examples={"application/json":
	 *              {
	 *					"id": 4,
	 *					"name": "Іван",
	 *					"surname": null,
	 *					"phone": null,
	 *					"town": null,
	 *					"region": null,
	 *					"email": "example@mail.com",
	 *					"photo": null,
	 *     				"usertype": 2,
	 *					"created_at": "2018-08-01 15:26:05",
	 *					"updated_at": "2018-08-01 15:26:05"
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
	public function user(Request $request)
	{
		return response()->json($request->user());
	}
}