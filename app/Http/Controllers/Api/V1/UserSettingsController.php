<?php

namespace Toecyd\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Mail;
use Toecyd\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Toecyd\Mail\NotificationMail;
use Toecyd\User;
use Toecyd\UserSettings;

/**
 * Class UserSettingsController
 * @package Toecyd\Http\Controllers
 * Всі маршрути для налаштувань користувача
 */
class UserSettingsController extends Controller
{
	/**
	 * Get the bookmarks of user
	 *
	 * @SWG\Get(
	 *     path="/user/settings",
	 *     summary="Отримати налаштування",
	 *     description="Отримати налаштування поточного користувача",
	 *     operationId="user-settings",
	 *     produces={"application/json"},
	 *     tags={"Особистий кабінет"},
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
	 *          @SWG\Property(property="name", type="string", example="Іван", description="Ім'я"),
	 *          @SWG\Property(property="surname", type="string", example="Коваленко", description="Прізвище"),
	 *          @SWG\Property(property="phone", type="string", example="0971234567", description="номер телефону"),
	 *          @SWG\Property(property="email", type="string", example="example@gmail.com", description="email користувача"),
	 *          @SWG\Property(property="photo", type="string", example="https://host/img/users/1542.jpg", description="посилання на фото профілю"),
	 *          @SWG\Property(property="email_notification_1", type="bool", example="1", description="надсилати повідомлення на email якщо в судді, якого користувач відстежує змінився статус"),
	 *          @SWG\Property(property="email_notification_2", type="bool", example="1", description="надсилати повідомлення на email якщо по справі яку користувач відстежує додалось нове судове засідання"),
	 *          @SWG\Property(property="email_notification_3", type="bool", example="1", description="надсилати повідомлення на email якщо по справі яку користувач відстежує в будь-якого судді змінився статус"),
	 *          @SWG\Property(property="email_notification_4", type="bool", example="1", description="надсилати повідомлення на email за 1 день до судового засідання яке користувач відстежує"),
	 *          @SWG\Property(property="email_notification_5", type="bool", example="0", description="надсилати повідомлення на email про пропозиції судової практики для користувача"),
	 *          @SWG\Property(property="email_notification_6", type="bool", example="0", description="надсилати повідомлення на email про новини, пропозиції, оновлення")
	 *        ),
	 *           examples={"application/json":
	 *               {
	 *                    "profile": {
	 *                      "name": "Іван",
	 *                      "surname": null,
	 *                      "phone": null,
	 *                      "email": "example@mail.com",
	 *                      "photo": null
	 *                      }
	 *                    "notifications": {
	 *                      "email_notification_1": 1,
	 *                      "email_notification_2": 1,
	 *                      "email_notification_3": 0,
	 *                      "email_notification_4": 1,
	 *                      "email_notification_5": 0,
	 *                      "email_notification_6": 1
	 *                    }
	 *                    
	 *                }
	 *            }
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
	public function indexSettings() {
		$user_settings = User::getSettings();
		return  response()->json($user_settings);
	}
	
	
	/**
	 *
	 * @SWG\Post(
	 *     path="/user/settings/password",
	 *     summary="Змінити пароль",
	 *     description="Змінити пароль поточного користувача",
	 *     operationId="user-settings-pass",
	 *     produces={"application/json"},
	 *     tags={"Особистий кабінет"},
	 *     security={
	 *     {"passport": {}},
	 *      },
	 *     @SWG\Parameter(
	 *      ref="#/parameters/Content-Type",
	 *     ),
	 *     @SWG\Parameter(
	 *      ref="#/parameters/X-Requested-With",
	 *     ),
	 *     @SWG\Parameter(
	 *     name="Старий і новий паролі",
	 *     in="body",
	 *     required=true,
	 *     description="Щоб змінити пароль поточного користувача, потрібно передати 2 параметри, його старий пароль і новий. Питання про підтвердження паролю користувачем залишається на Вашій стороні.",
	 *     @SWG\Schema(
	 *          type="object",
	 *            required={"old_password", "new_password"},
	 *          @SWG\Property(property="old_password", type="string", example="123456", description="Старий пароль Користувача"),
	 *          @SWG\Property(property="new_password", type="string", example="654321", description="Новий пароль Користувача (повинен містити від 6 до 32 символів)")
	 *       )
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Пароль успішно змінено",
	 *           examples={"application/json":
	 *               {
	 *                  "message": "Пароль успішно змінено"
	 *              }
	 *            }
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Необхідна аутентифікація користувача, або передано невалідні дані. Можливо токен не існує, або неправильний старий пароль",
	 *         examples={"application/json":
	 *              {
	 *                  "message": "Unauthenticated",
	 *              }
	 *          }
	 *     ),
	 *     @SWG\Response(
	 *         response=405,
	 *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом GET, хоча очікується POST.",
	 *     ),
	 *     @SWG\Response(
	 *         response=422,
	 *         description="Передані не валідні дані, або їх не достатньо, у відповіді буде зазначено у чому причина",
	 *         examples={"application/json":
	 *              {
	 *                  "message": "old_password є обовязковим параметром",
	 *              }
	 *          }
	 *     ),
	 * )
	 *
	 * @return [json] user object
	 */
	public function changePassword(Request $request) {
		$request->validate([
			'old_password' => 'required',
			'new_password' => 'required|string|min:6|max:32',
		]);
		$user = Auth::user();
		// перевірка чи коректно ввів користувач старий пароль
		if (!Hash::check(Input::get('old_password'), $user->password)) {
			return response()->json([
				'message' => Lang::get('passwords.incorrect')
			], 401);
		}
		// зберігаємо новий пароль
		$user->password = Hash::make(Input::get('new_password'));
		$user->save();
		
		return response()->json([
			'message' => Lang::get('passwords.updated')
		], 200);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 *
	 * @SWG\Post(
	 *     path="/user/settings/user-data",
	 *     summary="Змінити дані користувача",
	 *     description="Змінити дані поточного користувача. Ім'я прізвище, номер телефону і т.п",
	 *     operationId="user-settings-userdata",
	 *     produces={"application/json"},
	 *     tags={"Особистий кабінет"},
	 *     security={
	 *     {"passport": {}},
	 *      },
	 *     @SWG\Parameter(
	 *      ref="#/parameters/Content-Type",
	 *     ),
	 *     @SWG\Parameter(
	 *      ref="#/parameters/X-Requested-With",
	 *     ),
	 *     @SWG\Parameter(
	 *     name="Дані, які потрібно оновити",
	 *     in="body",
	 *     required=true,
	 *     description="Щоб змінити дані про користувача, потрібно передати лише ті нові дані, які необхідно змінити.",
	 *     @SWG\Schema(
	 *          type="object",
	 *          @SWG\Property(property="new_name", type="string", example="Іван", description="Нове імʼя користувача (повинно складатися від 3 до 255 символів)"),
	 *          @SWG\Property(property="new_surname", type="string", example="Шевченко", description="Нове прізвище користувача (повинно складатися від 3 до 255 символів)"),
	 *          @SWG\Property(property="new_phone", type="string", example="0971234567", description="Новий мобільний номер телефону Користувача (повинен містити від 9 до 12 символів без пробілів)")
	 *       )
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Дані успішно оновлено",
	 *           examples={"application/json":
	 *               {
	 *                  "message": "Дані оновлено"
	 *              }
	 *            }
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Необхідна аутентифікація користувача, або передано невалідні дані.",
	 *         examples={"application/json":
	 *              {
	 *                  "message": "Unauthenticated",
	 *              }
	 *          }
	 *     ),
	 *     @SWG\Response(
	 *         response=405,
	 *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом GET, хоча очікується POST.",
	 *     ),
	 *     @SWG\Response(
	 *         response=422,
	 *         description="Передані не валідні дані, або їх не достатньо, у відповіді буде зазначено у чому причина",
	 *         examples={"application/json":
	 *              {
	 *                  "message": "new_name може містити лише літери",
	 *              }
	 *          }
	 *     ),
	 * )
	 *
	 * @return [json] user object
	 */
	public function changeUserData(Request $request) {
		$request->validate([
			'new_name'    => 'string|min:3|max:255',
			'new_surname' => 'string|min:3|max:255',
			'new_phone' => 'string|min:9|max:10',
		]);
		
		$user = Auth::user();
		
		// якщо є будь-які вхідні дані оновлюємо їх для користувача
		if (Input::has('new_name')) {
			$user->name = Input::get('new_name');
		}
		if (Input::has('new_surname')) {
			$user->surname = Input::get('new_surname');
		}
		if (Input::has('new_phone')) {
			$user->phone = Input::get('new_phone');
		}
		
		// зберігаємо нові дані
		$user->save();
		
		return response()->json([
			'message' => Lang::get('auth.updated')
		], 200);
	}
	
	
	/**
	 *
	 * @SWG\Post(
	 *     path="/user/settings/notification",
	 *     summary="Змінити налаштування повідомлень",
	 *     description="Змінити налаштування повідомлень для поточного користувача.",
	 *     operationId="user-settings-notification",
	 *     produces={"application/json"},
	 *     tags={"Особистий кабінет"},
	 *     security={
	 *     {"passport": {}},
	 *      },
	 *     @SWG\Parameter(
	 *      ref="#/parameters/Content-Type",
	 *     ),
	 *     @SWG\Parameter(
	 *      ref="#/parameters/X-Requested-With",
	 *     ),
	 *     @SWG\Parameter(
	 *     name="Дані, які потрібно оновити",
	 *     in="body",
	 *     required=true,
	 *     description="Щоб змінити дані про користувача, потрібно передати лише ті нові дані, які необхідно змінити.",
	 *     @SWG\Schema(
	 *          type="object",
	 *          @SWG\Property(property="email_notification_1", type="integer", example="1", description="надсилати повідомлення на email якщо в судді, якого користувач відстежує змінився статус"),
	 *          @SWG\Property(property="email_notification_2", type="integer", example="1", description="надсилати повідомлення на email якщо по справі яку користувач відстежує додалось нове судове засідання"),
	 *          @SWG\Property(property="email_notification_3", type="integer", example="0", description="надсилати повідомлення на email якщо по справі яку користувач відстежує в будь-якого судді змінився статус"),
	 *          @SWG\Property(property="email_notification_4", type="integer", example="1", description="надсилати повідомлення на email за 1 день до судового засідання яке користувач відстежує"),
	 *          @SWG\Property(property="email_notification_5", type="integer", example="1", description="надсилати повідомлення на email про пропозиції судової практики для користувача"),
	 *          @SWG\Property(property="email_notification_6", type="integer", example="0", description="надсилати повідомлення на email про новини, пропозиції, оновлення")
	 *       )
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=200,
	 *         description="Дані успішно оновлено",
	 *           examples={"application/json":
	 *               {
	 *                  "message": "Дані оновлено"
	 *              }
	 *            }
	 *     ),
	 *
	 *     @SWG\Response(
	 *         response=401,
	 *         description="Необхідна аутентифікація користувача, або передано невалідні дані.",
	 *         examples={"application/json":
	 *              {
	 *                  "message": "Unauthenticated",
	 *              }
	 *          }
	 *     ),
	 *     @SWG\Response(
	 *         response=405,
	 *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом GET, хоча очікується POST.",
	 *     ),
	 *     @SWG\Response(
	 *         response=422,
	 *         description="Передані не валідні дані, або їх не достатньо, у відповіді буде зазначено у чому причина",
	 *         examples={"application/json":
	 *              {
	 *                  "message": "The given data was invalid.",
	 *                  "errors": {
	 *                  "email_notification_3": {
	 *                  "email_notification_3 повинен бути числом.",
	 *                  "максимальне значення для email_notification_3 = 1."
	 *                  }
	 *                  }
	 *              }
	 *          }
	 *     ),
	 * )
	 *
	 * @return [json] user object
	 */
	public function changeNotifications(Request $request) {
		$request->validate([
			'email_notification_1'    => 'numeric|min:0|max:1',
			'email_notification_2'    => 'numeric|min:0|max:1',
			'email_notification_3'    => 'numeric|min:0|max:1',
			'email_notification_4'    => 'numeric|min:0|max:1',
			'email_notification_5'    => 'numeric|min:0|max:1',
			'email_notification_6'    => 'numeric|min:0|max:1'
		]);
		
		$user_settings = UserSettings::find(Auth::user()->id);
		
		// якщо є будь-які вхідні дані оновлюємо їх
		if (Input::has('email_notification_1')) {
			$user_settings->email_notification_1 = Input::get('email_notification_1');
		}
		if (Input::has('email_notification_2')) {
			$user_settings->email_notification_2 = Input::get('email_notification_2');
		}
		if (Input::has('email_notification_3')) {
			$user_settings->email_notification_3 = Input::get('email_notification_3');
		}
		if (Input::has('email_notification_4')) {
			$user_settings->email_notification_4 = Input::get('email_notification_4');
		}
		if (Input::has('email_notification_5')) {
			$user_settings->email_notification_5 = Input::get('email_notification_5');
		}
		if (Input::has('email_notification_6')) {
			$user_settings->email_notification_6 = Input::get('email_notification_6');
		}
		
		// зберігаємо нові дані
		$user_settings->save();
		
		return response()->json([
			'message' => Lang::get('auth.updated')
		], 200);
	}
}
