<?php

namespace Toecyd\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Toecyd\Http\Controllers\Controller;
use Toecyd\Mail\AccountMail;
use Toecyd\PasswordReset;
use Toecyd\User;

class ResetPasswordController extends Controller
{
    /**
     * Надсилає на email листа з токеном для зміни пароля
     *
     * @SWG\Post(
     *     path="/user/password/reset",
     *     summary="Змінити пароль",
     *     description="Надсилає на email листа з токеном для зміни пароля",
     *     operationId="reset-password-reset",
     *     produces={"application/json"},
     *     tags={"Автентифікація користувача"},
     *
     *     @SWG\Parameter(
     *        ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *        ref="#/parameters/X-Requested-With",
     *     ),
     *
     *     @SWG\Parameter(
     *       name="Дані користувача",
     *       in="body",
     *       description="Для даного маршруту треба передати email користувача",
     *       @SWG\Schema(
     *            type="object",
     *            required={"email"},
     *            @SWG\Property(property="email",  type="string", example="example@gmail.com", description="email Користувача")
     *        )
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="",
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані (email не існує в БД, або запит на зміну пароля вже створено)",
     *           examples={"application/json":
     *              {
     *                    "message": "The selected email is invalid",
     *              }
     *            }
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом GET, хоча очікується що дані будуть надіслані методом POST.",
     *     ),
     * )
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users|unique:password_resets|max:255',
        ]);

        $token = str_random(60);
        $password_reset = new PasswordReset(['email' => $request->email, 'token' => $token]);
        $password_reset->save();

        Mail::to($request->email)
            ->send(new AccountMail('reset_password', 'Скидання паролю', [
                'token' => $token,
            ]));

        return response()->json([], 200);
    }

    /**
     * Записує в БД змінений пароль
     *
     * @SWG\Post(
     *     path="/user/password/new",
     *     summary="Записати в БД змінений пароль",
     *     description="Записує в БД змінений пароль",
     *     operationId="reset-password-create",
     *     produces={"application/json"},
     *     tags={"Автентифікація користувача"},
     *
     *     @SWG\Parameter(
     *        ref="#/parameters/Content-Type",
     *     ),
     *     @SWG\Parameter(
     *        ref="#/parameters/X-Requested-With",
     *     ),
     *
     *     @SWG\Parameter(
     *       name="Дані користувача",
     *       in="body",
     *       description="Для даного маршруту треба передати токен і новий пароль користувача",
     *       @SWG\Schema(
     *            type="object",
     *            required={"token", "password"},
     *            @SWG\Property(property="token",  type="string", example="N5FsJdlcmDd0GKVtLOFHvF26F_lxi21M6fbIOTDBAiSREPIkwWe1Z6qBhPIyLOsi4vaBSQ", description="токен користувача"),
     *            @SWG\Property(property="password",  type="string", example="1234Abcd", description="новий пароль користувача")
     *        )
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="",
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Передані не валідні дані (токен не існує в БД, або пароль не відповідає вимогам)",
     *           examples={"application/json":
     *              {
     *                    "message": "The selected password is invalid",
     *              }
     *            }
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Метод, з яким виконувався запит, не дозволено використовувати для заданого ресурсу; наприклад, запит був здійснений за методом GET, хоча очікується що дані будуть надіслані методом POST.",
     *     ),
     * )
     */
    public function create(Request $request)
    {
        $request->validate([
            'token'    => 'required|string|exists:password_resets',
            'password' => 'required|string|min:6|max:32',
        ]);

        $password_reset = PasswordReset::where('token', $request->token)->first();
        $user = User::where('email', $password_reset->email)->first();
        $user->password = bcrypt($request->password);

        $user->save();
        $password_reset->delete();

        return response()->json([], 200);
    }
}
