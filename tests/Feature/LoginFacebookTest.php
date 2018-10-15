<?php

namespace Tests\Feature;

use Toecyd\User;

class LoginFacebookTest extends BaseApiTest
{
    public function testLoginWithoutParams()
    {
        $this->post($this->url, [], $this->headers)->assertStatus(422);
    }

    /* Авторизуємося під уже існуючим користувачем */
    public function testLoginExistingUser()
    {
        $response = $this->post($this->url, $this->getDataToPost(), $this->headers);
        $response->assertStatus(200);
        $this->assertToken($response);

        $userUpdated = User::where('email', $this->user_data['email'])->first();

        $this->assertContains($userUpdated->id . '.jpg', $userUpdated->photo);
    }

    /* Авторизуємося під уже існуючим користувачем, змінуємо атрибут і перевіряємо успішність зміни */
    public function testLoginExistingUserWithUpdate()
    {
        $data = $this->getDataToPost();
        $data['surname'] .= '_updated';

        $response = $this->post($this->url, $data, $this->headers);
        $response->assertStatus(200);
        $this->assertToken($response);

        $userUpdated = User::where('email', $this->user_data['email'])->first();

        $this->assertContains($userUpdated->id . '.jpg', $userUpdated->photo);
        foreach (['email', 'name', 'surname'] as $key) {
            $this->assertEquals($userUpdated->$key, $data[$key]);
        }
    }

    /* Авторизуємося під неіснуючим користувачем. Система має нас зареєструвати */
    public function testLoginNonExistingUser()
    {
        // видаляємо користувача з БД. Віднині $user у нас -- неіснуючий
        $this->user->delete();

        $response = $this->post($this->url, $this->getDataToPost(), $this->headers);

        $response->assertStatus(201);
        $this->assertToken($response);

        $user_inserted = User::where('email', $this->user_data['email'])->first();
        $this->assertTrue(!empty($user_inserted));
        $this->assertContains($user_inserted->id . '.jpg', $user_inserted->photo);
        $this->assertEquals(2, $user_inserted->usertype);
    }

    /* Пробуємо авторизуватись по неіснуючому facebook_id */
    public function testLoginWithInvalidFacebookId()
    {
        $data = $this->getDataToPost();
        $data['id'] .= '12345';

        $this->post($this->url, $data, $this->headers)->assertStatus(401);
    }

    /**
     * @param $key
     * @param $min_len
     * @param $max_len
     *
     * @dataProvider providerLoginForValidationFail
     */
    public function testLoginForValidationFail($key, $min_len, $max_len)
    {
        $data = $this->getDataToPost();

        // Перевіряємо, що при замалій довжині атрибута отримаємо помилку
        $data[$key] = str_repeat('1', $min_len - 1);
        $this->post($this->url, $data, $this->headers)->assertStatus(422);

        // Перевіряємо, що при завеликій довжині атрибута отримаємо помилку
        $data[$key] = str_repeat('1', $max_len + 1);
        $this->post($this->url, $data, $this->headers)->assertStatus(422);
    }

    public function providerLoginForValidationFail()
    {
        return [
            ['id', 12, 25],
            ['name', 3, 255],
            ['surname', 3, 255],
        ];
    }

    public function setUp()
    {
        $this->user_data['facebook_id'] = '100001887847445';

        parent::setUp();

        $this->url .= 'login/facebook';
    }

    private function getDataToPost()
    {
        return [
            'id'      => $this->user_data['facebook_id'],
            'email'   => $this->user_data['email'],
            'name'    => $this->user_data['name'],
            'surname' => $this->user_data['surname'],
        ];
    }
}
