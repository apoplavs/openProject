<?php

namespace Tests\Feature;

class UserTest extends BaseApiTest
{
    public function setUp() {
        parent::setUp();

        $this->url .= 'user';
    }

    /**
     *  Авторизуємось і перевіряємо, чи вірно відпрацює запит даних користувача
     */
    public function testSuccess()
    {
        // Авторизуємось і отримуємо токен
        $headers_with_token = $this->headersWithToken($this->login($this->user_data));
        // Коли авторизовані, пробуємо отримати дані користувача
        $response = $this->get($this->url, $headers_with_token);
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
        // Авторизуємось і отримуємо токен
        $headers_with_token = $this->headersWithToken($this->login($this->user_data));
        $headers_with_token['Authorization'] .= '_wrong';

        // Коли авторизовані, пробуємо отримати дані користувача
        $response = $this->get($this->url, $headers_with_token);
        $response->assertStatus(401);
    }
}
