<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Toecyd\Http\Controllers\Api\V1\AuthController;

class LoginTest extends BaseApiTest
{
    public function setUp() {
        parent::setUp();

        $this->url .= 'login';
    }

    /**
     * Базовий тест на успішну авторизацію
     */
    public function testSuccessLogin()
    {
        $response = $this->login($this->user_data);
        $this->assertToken($response);
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
        $user_data = $this->user_data;

        if (!empty($remember_me)) {
            $user_data['remember_me'] = $remember_me;
        }

        $response = $this->login($user_data);
        $response->assertStatus($status);

        if ($status == 200) {
            $this->assertToken($response);

            $response_data = $response->decodeResponseJson();

            // $expires_at обчислюється як дата запуску всього набору тестів
            // тому оцінку різниці між $expires_at та $response_data['expires_at'] треба ставити більшою,
            // ніж тривалість виконання всього набору тестів
            $this->assertTrue(abs(strtotime($expires_at) - strtotime($response_data['expires_at'])) < 3600);
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
     * Тест на авторизацію неіснуючим користувачем
     */
    public function testNonExistingUser()
    {
        // видаляємо користувача; відтепер він -- неіснуючий
        $this->user->delete();

        $this->login($this->user_data)->assertStatus(401);
    }

    /**
     * Тест на авторизацію існуючим користувачем з невірним паролем
     */
    public function testWrongPassword()
    {
        $this->login(
            array_replace($this->user_data, ['password' => $this->user_data['password'] . '_wrong'])
        )->assertStatus(401);
    }
}
