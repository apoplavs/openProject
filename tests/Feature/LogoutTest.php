<?php

namespace Tests\Feature;

class LogoutTest extends BaseApiTest
{
    public function setUp() {
        parent::setUp();

        $this->url .= 'logout';
    }

    /**
     * Вихід користувача і спроба зайти на сайт з тим же токеном
     */
    public function testSuccessLogout()
    {
        // Авторизуємось
        $response = $this->login($this->user_data);
        $response->assertStatus(200);

        // Пробуємо вийти з системи
        $headers_with_token = $this->headersWithToken($response);
        $this->get($this->url, $headers_with_token)->assertStatus(200);

        // Пробуємо вдруге вийти з системи з тим же токеном. Маємо отримати відповідь "401 Unauthorized"
        $this->get($this->url, $headers_with_token)->assertStatus(401);
    }
}
