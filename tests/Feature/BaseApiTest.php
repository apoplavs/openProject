<?php

namespace Tests\Feature;

use Toecyd\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BaseApiTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Заголовки для HTTP-запитів
     */
    protected $headers = ['accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'];

    /**
     * Імена полів, які мають бути заповненими, коли ми отримуємо токен
     */
    protected $token_keys = ['access_token', 'token_type', 'expires_at'];

    /**
     * Дані про тестового користувача; мають бути такими, яких нема у БД
     */
    protected $user_data = [
        'name' => 'slualexvas_test_name',
        'surname'   => 'slualexvas_test_surname',
        'email'     => 'slualexvas@gmail.com',
        'password'  => 'test_password',
    ];

    /* @var User */
    protected $user;

    /* URL для HTTP-запитів */
    protected $url = 'api/v1/';

    public function setUp() {
        parent::setUp();

        $local_user_data = $this->user_data;
        $local_user_data['password'] = bcrypt($local_user_data['password']);
        $this->user = new User($local_user_data);
        $this->user->save();
    }

    /**
     * Авторизація
     *
     * @param array $user_data
     *
     * @return TestResponse
     */
    protected function login($user_data)
    {
        $login_data = array_intersect_key($user_data, array_flip(['email', 'password', 'remember_me']));
        return $this->post('api/v1/login', $login_data, $this->headers);
    }

    /**
     * Повертаємо headers, до яких доданий токен
     *
     * @param TestResponse $login_response
     *
     * @return array
     */
    protected function headersWithToken($login_response)
    {
        $response_data = $login_response->decodeResponseJson();

        $this->assertNotEmpty($response_data['token_type']);
        $this->assertNotEmpty($response_data['access_token']);

        return array_merge($this->headers,
            ['Authorization' => $response_data['token_type'] . ' ' . $response_data['access_token']]
        );
    }

    /**
     * Перевіряємо, що в $response є токен з усіма полями
     *
     * @param TestResponse $response
     *
     * @return array
     */
    protected function assertToken($response)
    {
        $response_data = $response->decodeResponseJson();
        foreach ($this->token_keys as $key) {
            $this->assertNotEmpty($response_data[$key]);
        }
    }

    /**
     * Щоб не викидався warning про те, що в цьому класі нема тестів
     */
    public function testTrivial()
    {
        $this->assertTrue(true);
    }
}
