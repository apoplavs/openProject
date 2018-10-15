<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SignupTest extends TestCase
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
    private $url = 'api/v1/signup';

    /**
     * Реєстрація користувача
     */
    public function testSuccessSignup()
    {
        // Пробуємо авторизуватись. Авторизація має бути провальною, бо такий користувач ще не реєструвався
        $login_url = 'api/v1/login';
        $login_data = array_intersect_key($this->user_data, array_flip(['email', 'password']));
        $response = $this->post($login_url, $login_data, $this->headers);
        $response->assertStatus(401);

        // Реєструємось
        $response = $this->post($this->url, $this->user_data, $this->headers);
        $response->assertStatus(201);

        // Знову пробуємо авторизуватись. Тепер авторизація має бути успішною
        $response = $this->post($login_url, $login_data, $this->headers);
        $response->assertStatus(200);
    }

    /**
     * Реєстрація користувача
     */
    public function testErrorExistingUser()
    {
        // Реєструємось
        $response = $this->post($this->url, $this->user_data, $this->headers);
        $response->assertStatus(201);

        // Реєструємось ще раз з тими ж даними
        $response = $this->post($this->url, $this->user_data, $this->headers);
        $response->assertStatus(422);
    }

    /**
     * Реєстрація з неповними даними
     * @param $key
     *
     * @dataProvider providerRequiredKeys
     */
    public function testErrorIncompleteData($key)
    {
        $user_data_local = $this->user_data;
        unset($user_data_local[$key]);

        $response = $this->post($this->url, $user_data_local, $this->headers);
        $response->assertStatus(422);
    }

    /**
     * @return array
     */
    public function providerRequiredKeys() {
        return [
            ['name'],
            ['email'],
            ['password'],
        ];
    }

    /**
     * Реєстрація з занадто довгими або короткими даними
     * @param $attr_name
     * @param $min_len
     * @param $max_len
     *
     * @dataProvider providerErrorValidation
     */
    public function testErrorValidation($attr_name, $min_len, $max_len)
    {
        $user_data_local = $this->user_data;
        if (!empty($min_len)) {
            $user_data_local[$attr_name] = str_repeat('a', $min_len - 1);
            $response = $this->post($this->url, $user_data_local, $this->headers);
            $response->assertStatus(422);
        }

        if (!empty($max_len)) {
            $user_data_local[$attr_name] = str_repeat('a', $max_len + 1);
            $response = $this->post($this->url, $user_data_local, $this->headers);
            $response->assertStatus(422);
        }
    }

    /**
     * @return array
     */
    public function providerErrorValidation() {
        return [
            ['name', 3, 255],
            ['email', 0, 255],
            ['password', 6, 32],
        ];
    }
}
