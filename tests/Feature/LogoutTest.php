<?php

namespace Tests\Feature;

use Toecyd\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogoutTest extends TestCase
{
    use DatabaseTransactions;

    /* Заголовки для HTTP-запитів */
    private $headers = ['accept' => 'application/json'];

    /* Дані про тестового користувача; мають бути такими, яких нема у БД */
    private $user_data = [
        'name' => 'slualexvas_test_name',
        'email'     => 'slualexvas@gmail.com',
        'password'  => 'test_password',
    ];

    /* URL для HTTP-запитів */
    private $url = 'api/v1/logout';

    public function setUp() {
        parent::setUp();

        $local_user_data = $this->user_data;
        $local_user_data['password'] = bcrypt($local_user_data['password']);
        $user = new User($local_user_data);
        $user->save();
    }

    /**
     * Вихід користувача і спроба зайти на сайт з тим же токеном
     */
    public function testSuccessLogout()
    {
        // Авторизуємось
        $data = [
            'email' => $this->user_data['email'],
            'password' => $this->user_data['password'],
        ];
        $response = $this->post('api/v1/login', $data, $this->headers);
        $response->assertStatus(200);
        $response_data = $response->decodeResponseJson();
        $this->assertNotEmpty($response_data['token_type']);
        $this->assertNotEmpty($response_data['access_token']);

        // Коли авторизовані, пробуємо вийти з системи
        $headers = array_merge($this->headers, ['Authorization' => $response_data['token_type'] . ' ' . $response_data['access_token']]);
        $response = $this->get($this->url, $headers);
        $response->assertStatus(200);

        // Пробуємо вдруге вийти з системи з тим же токеном. Маємо отримати відповідь "401 Unauthorized"
        $response = $this->get($this->url, $headers);
        $response->assertStatus(401);
    }
}
