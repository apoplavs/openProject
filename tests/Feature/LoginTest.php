<?php

namespace Tests\Feature;

use Toecyd\User;
use Carbon\Carbon;
use Tests\TestCase;
use Toecyd\Http\Controllers\Api\V1\AuthController;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    /* URL для HTTP-запитів */
    private $url = 'api/v1/login';

    /* Заголовки для HTTP-запитів */
    private $headers = ['accept' => 'application/json'];

    /* Дані про тестового користувача; мають бути такими, яких нема у БД */
    private $user_data = [
        'name' => 'slualexvas_test_name',
        'email'     => 'slualexvas@gmail.com',
        'password'  => 'test_password',
    ];

    /* @var User */
    private $user;

    /* Імена полів, які мають бути заповненими, коли ми отримуємо токен*/
    private $token_keys = ['access_token', 'token_type', 'expires_at'];

    public function setUp() {
        parent::setUp();

        $local_user_data = $this->user_data;
        $local_user_data['password'] = bcrypt($local_user_data['password']);
        $this->user = new User($local_user_data);
        $this->user->save();
    }

    /**
     * Базовий тест на успішну авторизацію
     */
    public function testSuccessLogin()
    {
        $data = [
            'email' => $this->user_data['email'],
            'password' => $this->user_data['password'],
        ];

        $response = $this->post($this->url, $data, $this->headers);
        $response->assertStatus(200);

        $response_data = $response->decodeResponseJson();
        foreach ($this->token_keys as $key) {
            $this->assertNotEmpty($response_data[$key]);
        }
    }

    /**
     * @param $remember_me
     * @param $status
     * @param $expires_at
     *
     * @dataProvider providerRememberMe
     */
    public function testRememberMe(int $remember_me, int $status, string $expires_at)
    {
        $data = [
            'email' => $this->user_data['email'],
            'password' => $this->user_data['password'],
        ];

        if (!empty($remember_me)) {
            $data['remember_me'] = $remember_me;
        }

        $response = $this->post($this->url, $data, $this->headers);
        $response->assertStatus($status);

        if ($status == 200) {
            $response_data = $response->decodeResponseJson();
            foreach ($this->token_keys as $key) {
                $this->assertNotEmpty($response_data[$key]);
            }

            $etalon_date = date('Y-m-d', strtotime($expires_at));
            $test_date = date('Y-m-d', strtotime($response_data['expires_at']));
            $this->assertEquals($etalon_date, $test_date);
        }
    }

    /**
     * @return array
     */
    public function providerRememberMe() {
        return [
            [0, 200, Carbon::now(AuthController::TIMEZONE)->addDay()],
            [1, 200, Carbon::now(AuthController::TIMEZONE)->addWeeks(2)],
            [2, 200, Carbon::now(AuthController::TIMEZONE)->addMonths(6)],
            [3, 200, Carbon::now(AuthController::TIMEZONE)->addYears(5)],
            [4, 422, ''],
        ];
    }

    /**
     * Тест на авторизацію користувачем, email якого відсутній в базі
     */
    public function testWrongEmail()
    {
        $data = [
            'email' => $this->user_data['email'],
            'password' => $this->user_data['password'],
        ];

        // видаляємо користувача; відтепер він -- неіснуючий
        $this->user->delete();

        $response = $this->post($this->url, $data, $this->headers);
        $response->assertStatus(401);
    }

    /**
     * Тест на авторизацію неіснуючим користувачем
     */
    public function testWrongPassword()
    {
        $data = [
            'email' => $this->user_data['email'],
            'password' => $this->user_data['password'] . 'wrong',
        ];

        $response = $this->post($this->url, $data, $this->headers);
        $response->assertStatus(401);
    }
}
