<?php

namespace Tests\Feature;

class SignupTest extends BaseApiTest
{
    public function setUp() {
        parent::setUp();

        $this->user->delete();
        $this->url .= 'signup';
    }

    /**
     * Реєстрація користувача
     */
    public function testSuccessSignup()
    {
        // Пробуємо авторизуватись. Авторизація має бути провальною, бо такий користувач ще не реєструвався
        $this->login($this->user_data)->assertStatus(401);

        // Реєструємось
        $response = $this->post($this->url, $this->user_data, $this->headers);
        $response->assertStatus(201);

        // Знову пробуємо авторизуватись. Тепер авторизація має бути успішною
        $this->login($this->user_data)->assertStatus(200);
    }

    /**
     * Якщо користувач уже існує, має видаватись помилка
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
    public function testErrorValidation($key, $min_len, $max_len)
    {
        $data = $this->user_data;

        // Перевіряємо, що при замалій довжині атрибута отримаємо помилку
        if (!empty($min_len)) {
            $data[$key] = str_repeat('a', $min_len - 1);
            $this->post($this->url, $data, $this->headers)->assertStatus(422);
        }

        // Перевіряємо, що при завеликій довжині атрибута отримаємо помилку
        if (!empty($max_len)) {
            $data[$key] = str_repeat('a', $max_len + 1);
            $this->post($this->url, $data, $this->headers)->assertStatus(422);
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
