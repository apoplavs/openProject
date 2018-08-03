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
 *           description="REST API проекту являє собою набір методів, за допомогою яких здійснюються запити і повертаються відповіді для кожної операції. Всі відповіді приходять у вигляді JSON структур. При передачі будь-яких даних в цьому API вам слід встановити два заголовки, наведені нижче:
 *	 Content-Type: application/json
 *	 X-Requested-With: XMLHttpRequest",
 *         @SWG\Contact(name="Developers", url="https://www.google.com"),
 *     ),
 *     @SWG\SecurityScheme(
 *          securityDefinition="default",
 *          type="apiKey",
 *          in="header",
 *            description="Токен авторизації, представлений як: 'Bearer \<Token\>'",
 *          name="Authorization"
 *      ),
 *
 *     @SWG\Definition(
 *     		definition="Token",
 * 			required={"access_token", "token_type"},
 *     example="Bearer",
 *          description="Кожен запит на любий маршрут крім /signup i /login повинен містити токен авторизації, який одночасно є ідентифікатором користувача. Його потрібно буде передавати в Headers при здійснені запиту
 * Authorization:  'Bearer j7zRhODMx...6IjYw'",
 *     		@SWG\Property(property="access_token", type="string", example="p0aSI6IjYwODMxY...jRhYz", description="Сам токен, (маркер доступу)"),
 * 			@SWG\Property(property="token_type", type="string", example="Bearer", description="Це зазвичай буде слово Bearer (для позначення носія)"),
 * 			@SWG\Property(property="expires_at", type="string", example="2019-09-02 18:21:42", description="Дата та час, що представляє TTL для токена (дата і час, коли термін дії токена закінчиться)"),
 *			@SWG\Property(property="refresh_token", type="string", description="Токен оновлення, який може бути використаний для придбання нового токену доступу, коли термін дії оригіналу закінчиться"),
 * 		),
 *

 * )
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
