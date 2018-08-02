<?php
/**
 * Created by PhpStorm.
 * User: apoplavs
 * Date: 01.08.18
 * Time: 17:04
 */

namespace Toecyd\Http\Controllers\Api\V1\Auth;


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
	 *     description="Зареєструвати нового користувача",
	 *     operationId="api.dashboard.index",
	 *     produces={"application/json"},
	 *     tags={"Аутентифікація користувача"},
	 *     summary="Реєстрація",
	 *
	 *     @SWG\Parameter(
	 *     name="Дані користувача",
	 *     in="body",
	 *     description="Щоб зареєструвати нового користувача в системі, потрібно надіслати його ім'я, email і пароль. Питання про підтвердження паролю користувачем залишається на Вашій стороні.
	 *   Ім'я повинно складатися від 3 до 255 символів
	 *	 email існуючий, валідний, унікальний в системі
	 *	 Пароль повинен бути від 6 до 32 символів",
	 *     required=true,
	 *     @SWG\Schema(
	 *          type="object",
	 *          @SWG\Property(property="name", type="string", example="NameOfUser", description="Ім'я Користувача"),
	 *          @SWG\Property(property="email", type="string", example="example@gmail.com", description="email Користувача"),
	 *          @SWG\Property(property="password", type="string", example="123456", description="Пароль Користувача")
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
	 *         description="Передані на валідні дані, або їх не достатньо, у відповіді буде зазначено у чому причина",
	 *     	   examples={"application/json":
	 *              {
	 *     				"message": "The given data was invalid.",
	 *                  "errors": {
	 *                  	"email": "Даний email вже використовується."
	 *     					}
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
	 * @param  [string] password_confirmation
	 * @return [string] message
	 */
	public function signup(Request $request)
	{
		//return (response()->json($request->all()));
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
	 * 
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
			'remember_me' => 'boolean'
		]);
		$credentials = request(['email', 'password']);
		if(!Auth::attempt($credentials))
			return response()->json([
				'message' => 'Unauthorized'
			], 401);
		$user = $request->user();
		$tokenResult = $user->createToken('Personal Access Token');
		$token = $tokenResult->token;
		if ($request->remember_me)
			$token->expires_at = Carbon::now()->addWeeks(1);
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
	 * @return [json] user object
	 */
	public function user(Request $request)
	{
		return response()->json($request->user());
	}
}