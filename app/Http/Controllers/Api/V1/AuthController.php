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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Lang;
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
	 *     				"message": "Користувач успішно створений"
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
			'message' => Lang::get('auth.successfully_created')
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
	 *	3 - п'ять років.
 Одночасно для одного користувача може бути видано до 5 токенів, при видачі кожного насупного - попередній 5-й анулююється, з них: 1 токен на п'ять років")
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
	 *     				"message": "Неправильний логін або пароль",
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
		$user_check = User::checkUser($request->email);
		// якщо в користувача неправильний пароль, або є інші причини чому він не може ввійти
		if(!Auth::attempt($credentials)	|| $user_check !== true) {
			return response()->json([
				'message' => ($user_check === true ? Lang::get('auth.failed') : $user_check)
			], 401);
		}
		// генерація токена користувача
		$token_result = $this->getToken($request);

		return response()->json([
			'access_token' => $token_result->accessToken,
			'token_type' => 'Bearer',
			'expires_at' => Carbon::parse(
				$token_result->token->expires_at
			)->toDateTimeString()
		]);
	}
	
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	private function getToken(Request $request) {
		// генеруємо токен
		$token_result = $request->user()->createToken('Personal Access Token');
		$token = $token_result->token;
		
		// визначаємо тривалість дії токена на основі запиту користувача
		if (!$request->remember_me) {
			$token->expires_at = Carbon::now('Europe/Kiev')->addDay();
		} else if ($request->remember_me == 1){
			$token->expires_at = Carbon::now('Europe/Kiev')->addWeeks(2);
		} else if ($request->remember_me == 2){
			$token->expires_at = Carbon::now('Europe/Kiev')->addMonths(6);
		} else if ($request->remember_me == 3){
			// генерація long time expires токена
			$token_result = $request->user()->createToken('LTE Token');
			$token = $token_result->token;
			$token->expires_at = Carbon::now('Europe/Kiev')->addYears(5);
			// анулюємо інші довготривалі токени
			DB::table('oauth_access_tokens')
				->where('user_id', '=', $request->user()->id)
				->where('name', '=', 'LTE Token')
				->update(['revoked' => 1]);
		} else {
			$token->expires_at = Carbon::now('Europe/Kiev')->addDay();
		}
		// зберігаємо токен в БД
		$token->save();
		return ($token_result);
	}

    /**
     * Авторизація через Google
     * @SWG\Post(
     *     path="/login/google",
     *     summary="Login Google",
     *     description="Логін через акаунт в Google",
     *     operationId="login_google",
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
     *     description="Щоб здійснити логін користувача через Google, потрібно надіслати його google_id, email, ім'я (можливо, ще й прізвище), посилання на Google-аккаунт та посилання на Google-аватарку.",
     *     @SWG\Schema(
     *          type="object",
     *     		required={"id", "email", "name", "link", "picture"},
     *          @SWG\Property(property="id", type="string", example="111483939504700006800", description="google_id Користувача (повинно складатися від 3 до 255 цифр)"),
     *          @SWG\Property(property="name", type="string", example="NameOfUser", description="Ім'я Користувача (повинно складатися від 3 до 255 символів)"),
     *          @SWG\Property(property="email", type="string", example="example@gmail.com", description="Email Користувача (існуючий, валідний, унікальний в системі)"),
     *          @SWG\Property(property="link", type="string", example="https://plus.google.com/111483939504700006800", description="Посилання на Google аккаунт користувача (валідний URL)"),
     *          @SWG\Property(property="picture", type="string", example="https://lh3.googleusercontent.com/-LC0h1Ai3sXg/W6XiqkKewLI/AAAAAAAAAMQ/olvDX7mRLlwgDnzE9ARggY3dXaNu7Rh-ACJkCGAYYCw/w1024-h576-n-rw-no/my-photo.jpg", description="Посилання на google-аватарку користувача")
     *       )
     *     ),
     *
     *     @SWG\Response(
     *         response=201,
     *         description="Користувач успішно зареєстрований",
     *     	   examples={"application/json":
     *              {
     *     				"token": "hfadlkfjha"
     *              }
     *     		}
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Користувач успішно залогінився",
     *     	   examples={"application/json":
     *              {
     *     				"token": "hfadlkfjha"
     *              }
     *     		}
     *     ),
     *     @SWG\Response(
     *         response=302,
     *         description="Передані не валідні дані по формі (наприклад, завелика довжина рядка)"
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Передані не валідні дані по змісту (наприклад, за URL не завантажується аватарка)"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом GET, хоча очікується що дані будуть надіслані методом POST.",
     *     )
     * )
     *
     * @param  [string] id
     * @param  [string] email
     * @param  [string] name
     * @param  [string] link
     * @param  [string] picture
     * @return [string] token
     */
	public function loginGoogle(Request $request)
    {
        $userAlreadyExists = true;

        $request->validate([
            'id' => 'required|string',
            'email' => 'required|string|email',
            'name' => 'required|string',
            'surname' => 'string',
            'link' => 'required|string',
            'picture' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (empty($user)) {
            $user = factory(User::class)->create([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            $userAlreadyExists = false;
        }
        $user->google_id = $request->id;
        $user->password = bcrypt($request->id);
        $user->surname = $request->surname;
        $user->usertype = 2;
        $user->remember_token = str_random(10);

        if ($this->getHttpStatusByUrl($request->link) != 200) {
            return response()->json([
                'message' => 'Unauthorized (bad link)'
            ], 401);
        }

        if (!$this->savePhoto($request->picture, $user)) {
            return response()->json([
                'message' => 'Unauthorized (bad picture)'
            ], 401);
        }

        $user->save();

        return response()->json([
            'token' => $user->remember_token
        ], $userAlreadyExists ? 200 : 201);
    }

    /**
     * Авторизація через Facebook
     * @SWG\Post(
     *     path="/login/facebook",
     *     summary="Login Facebook",
     *     description="Логін через акаунт в Facebook",
     *     operationId="login_facebook",
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
     *     description="Щоб здійснити логін користувача через Facebook, потрібно надіслати його facebook_id, email, ім'я (можливо, ще й прізвище)",
     *     @SWG\Schema(
     *          type="object",
     *     		required={"id", "email", "name"},
     *          @SWG\Property(property="id", type="string", example="100001887847445", description="google_id Користувача (повинно складатися від 3 до 255 цифр)"),
     *          @SWG\Property(property="name", type="string", example="NameOfUser", description="Ім'я Користувача (повинно складатися від 3 до 255 символів)"),
     *          @SWG\Property(property="email", type="string", example="example@gmail.com", description="Email Користувача (існуючий, валідний, унікальний в системі)"),
     *       )
     *     ),
     *
     *     @SWG\Response(
     *         response=201,
     *         description="Користувач успішно зареєстрований",
     *     	   examples={"application/json":
     *              {
     *     				"token": "hfadlkfjha"
     *              }
     *     		}
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Користувач успішно залогінився",
     *     	   examples={"application/json":
     *              {
     *     				"token": "hfadlkfjha"
     *              }
     *     		}
     *     ),
     *     @SWG\Response(
     *         response=302,
     *         description="Передані не валідні дані по формі (наприклад, завелика довжина рядка)"
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Передані не валідні дані по змісту (наприклад, за URL не завантажується аватарка)"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом GET, хоча очікується що дані будуть надіслані методом POST.",
     *     )
     * )
     *
     * @param  [string] id
     * @param  [string] email
     * @param  [string] name
     * @return [string] token
     */
    public function loginFacebook(Request $request)
    {
        $userAlreadyExists = true;

        $request->validate([
            'id' => 'required|string',
            'email' => 'required|string|email',
            'name' => 'required|string',
            'surname' => 'string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (empty($user)) {
            $user = factory(User::class)->create([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            $userAlreadyExists = false;
        }
        $user->facebook_id = $request->id;
        $user->password = bcrypt($request->id);
        $user->surname = $request->surname;
        $user->usertype = 2;
        $user->remember_token = str_random(10);

        if (!$this->savePhoto("https://graph.facebook.com/v3.1/{$request->id}/picture?width=200&height=200", $user)) {
            return response()->json([
                'message' => 'Unauthorized (bad picture)'
            ], 401);
        }

        $user->save();
        return response()->json([
            'token' => $user->remember_token
        ], $userAlreadyExists ? 200 : 201);
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
	 *     				"message": "Успішно вийшов із системи"
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
			'message' => Lang::get('auth.successfully_logout')
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
	
	
	
	
	
	
	
	

    private function getHttpStatusByUrl(string $url):int
    {
        $handle = curl_init($url);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

        curl_exec($handle);

        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        return $httpCode;
    }

    private function savePhoto($photoUrl, $user)
    {
        $fileExtension = pathinfo($photoUrl, PATHINFO_EXTENSION);
        if (empty($fileExtension)) {
            $fileExtension = 'jpg';
        }

        if (!in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
            return false;
        }

        $localPhotoUrl = "img/users/{$user->id}.{$fileExtension}";
        $photoContents = @file_get_contents($photoUrl);
        if (!$photoContents || !Storage::put($localPhotoUrl, file_get_contents($photoUrl))) {
            return false;
        }

        if (!Storage::exists($localPhotoUrl) || Storage::size($localPhotoUrl) == 0) {
            return false;
        }

        $user->photo = $localPhotoUrl;
        return true;
    }
}