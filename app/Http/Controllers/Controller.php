<?php

namespace Toecyd\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class Controller
 * опис для Swagger
 *
 * @SWG\Swagger(
 *     schemes={"http", "https"},
 *     host=L5_SWAGGER_CONST_HOST,
 *     basePath=L5_SWAGGER_BASE_PATH,
 *     produces={"application/json"},
 * 	   consumes={"application/json"},
 *     @SWG\Info(
 *         version="1.0",
 *         title="Документація для API проекту",
 *           description="REST API проекту являє собою набір методів, за допомогою яких здійснюються запити і повертаються відповіді для кожної операції. Всі відповіді приходять у вигляді JSON структур. Для коректної роботи, при передачі будь-яких даних в цьому API вам слід встановити два заголовки:
 *	 Content-Type: application/json
 *	 X-Requested-With: XMLHttpRequest",
 *         @SWG\Contact(name="Developers", url="https://www.google.com"),
 *     ),
 *     @SWG\SecurityScheme(
 *          securityDefinition="passport",
 *          type="apiKey",
 *          in="header",
 *     		flow="password",
 *     		scopes={},
 *          description="Токен авторизації, представлений як: 'Bearer \<Token\>'",
 *          name="Authorization"
 *      ),
 *
 *     @SWG\Definition(
 *     		definition="Token",
 * 			required={"access_token", "token_type", "expires_at"},
 *          description="Ми використовуємо протокол OAuth 2.0 для автентифікації та авторизації користувачів. Кожен запит на любий маршрут (крім /signup, /login, /guest/*, /judges/autocomplete) повинен містити токен авторизації, який одночасно є ідентифікатором користувача. Його потрібно передавати в Headers при здійснені запиту. Він повинен виглядати наступним чином:
 *     'Authorization: token_type access_token'
____________________________________________________________
     ПРИКЛАД:
 Headers:
 * * Content-Type: application/json
 * * X-Requested-With: XMLHttpRequest
 * * Authorization:  Bearer j7zRe7JO8-JaxjRyylhODMx . . . nk4GnDLwAibyja_ZDt1x6LnLAevmJkjYw",
 *     		@SWG\Property(property="access_token", type="string", example="p0aSI6IjYwODMxY...jRhYz", description="Сам токен, (маркер доступу)"),
 * 			@SWG\Property(property="token_type", type="string", example="Bearer", description="Це зазвичай буде слово Bearer (додається в Header перед access_token)"),
 * 			@SWG\Property(property="expires_at", type="string", example="2019-09-02 18:21:42", description="Дата та час, що представляє TTL для токена (дата і час, коли термін дії токена закінчиться)"),
 *			@SWG\Property(property="refresh_token", type="string", description="Токен оновлення, який може бути використаний для придбання нового токену доступу, коли термін дії оригіналу закінчиться"),
 * 		),
 *
 * 		@SWG\Definition(
 *     		definition="User",
 * 			required={"name", "email", "usertype", "created_at"},
 *          description="Кожного користувача в системі можна представити як окремий об'єкт із набором властивостей",
 *     		@SWG\Property(property="name", type="string", example="Іван", description="Ім'я"),
 *     		@SWG\Property(property="surname", type="string", example="Коваленко", description="Прізвище"),
 *     		@SWG\Property(property="phone", type="string", example="0971234567", description="номер телефону"),
 *     		@SWG\Property(property="facebook_id", type="string", example="100000317390816", description="id профілю facebook (якщо користувач зреєстрований через facebook)"),
 *     		@SWG\Property(property="google_id", type="string", example="101103303581561273014", description="id профілю google (якщо користувач зреєстрований через google)"),
 *     		@SWG\Property(property="email", type="string", example="example@gmail.com", description="email користувача"),
 *     		@SWG\Property(property="photo", type="string", example="https://host/img/users/1542.jpg", description="посилання на фото профілю"),
 *     		@SWG\Property(property="usertype", type="integer", example="1", description="тип користувача: 1. Зареєстрований користувач, з НЕ підтвердженим email; 2. Користувач з підтвердженим email; 3. PRO аккаунт"),
 *     		@SWG\Property(property="created_at", type="string", example="2018-08-17 15:26:05", description="дата і час реєстрації"),
 *     		@SWG\Property(property="updated_at", type="string", example="2018-10-21 19:42:03", description="дата і час останнього редагування даних"),
 * 		),
 *
 * 			@SWG\Parameter(
 *     			name="Content-Type",
 *     			type="string",
 *     			in="header",
 *     			required=true,
 *     			default="application/json"
 *     		),
 *     		@SWG\Parameter(
 *     			name="X-Requested-With",
 *     			type="string",
 *     			in="header",
 *     			required=true,
 *     			default="XMLHttpRequest"
 *     		)
 * )
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
