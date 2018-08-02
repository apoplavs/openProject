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
 *     schemes={"http"},
 *     host="toecyd.local",
 *     basePath="/api/v1",
 *     produces={"application/json"},
 * 	   consumes={"application/json"},
 *     @SWG\Info(
 *         version="1.0",
 *         title="Документація для API проекту",
 *           description="REST API проекту являє собою набір методів, за допомогою яких здійснюються запити і повертаються відповіді для кожної операції. Всі відповіді приходять у вигляді JSON структур. При передачі будь-яких даних в цьому API вам слід встановити два заголовки, наведені нижче:
 *	 Content-Type: application/json
 *	 X-Requested-With: XMLHttpRequest",
 *         @SWG\Contact(name="Developers", url="https://www.google.com"),
 *     )
 * )
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
