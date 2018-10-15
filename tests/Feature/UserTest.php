<?php

namespace Tests\Feature;

use Toecyd\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /* Заголовки для HTTP-запитів */
    private $headers = ['accept' => 'application/json'];

    /* URL для HTTP-запитів */
    private $url = 'api/v1/user';

    /* Дані про тестового користувача; мають бути такими, яких нема у БД */
    private $user_data = [
        'name' => 'slualexvas_test_name',
        'email'     => 'slualexvas@gmail.com',
        'password'  => 'test_password',
    ];

    /* @var User */
    private $user;

    public function setUp() {
        parent::setUp();

        $local_user_data = $this->user_data;
        $local_user_data['password'] = bcrypt($local_user_data['password']);
        $this->user = new User($local_user_data);
        $this->user->save();
    }

    /**
     *  Авторизуємось і перевіряємо, чи вірно відпрацює запит даних користувача
     */
    public function testSuccess()
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

        // Коли авторизовані, пробуємо отримати дані користувача
        $headers = array_merge($this->headers, ['Authorization' => $response_data['token_type'] . ' ' . $response_data['access_token']]);
        $response = $this->get($this->url, $headers);
        $response->assertStatus(200);

        $response_data_user = $response->decodeResponseJson();
        foreach (array_keys($this->user_data) as $key) {
            if ($key == 'password') {
                continue;
            }

            $this->assertEquals($this->user_data[$key], $response_data_user[$key]);
        }
    }

    /**
     *  Пробуємо зайти без авторизації
     */
    public function testErrorUnauthorized()
    {
        $response = $this->get($this->url, $this->headers);
        $response->assertStatus(401);
    }

    /**
     *  Авторизуємось і пробуємо зайти з невірним токеном
     */
    public function testErrorBadToken()
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

        // Коли авторизовані, пробуємо отримати дані користувача
        $headers = array_merge($this->headers, ['Authorization' => $response_data['token_type'] . ' ' . $response_data['access_token'] . '_wrong']);
        $response = $this->get($this->url, $headers);
        $response->assertStatus(401);
    }
}
