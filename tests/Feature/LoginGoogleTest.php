<?php

namespace Tests\Feature;

use Toecyd\User;

class LoginGoogleTest extends BaseApiTest
{
    /**
     * @var string
     */
    private $google_link = 'https://plus.google.com/111483939504700006800';

    /**
     * @var string
     */
    private $google_picture = 'https://lh3.googleusercontent.com/-LC0h1Ai3sXg/W6XiqkKewLI/AAAAAAAAAMQ/olvDX7mRLlwgDnzE9ARggY3dXaNu7Rh-ACJkCGAYYCw/w1024-h576-n-rw-no/my-photo.jpg';

    public function testLoginWithoutParams() {
        $this->post($this->url, [], $this->headers)->assertStatus(422);
    }

    /* Авторизуємося під уже існуючим користувачем */
    public function testLoginExistingUser() {
        $response = $this->post($this->url, $this->getDataToPost(), $this->headers);
        $this->assertToken($response);

        $userUpdated = User::where('email', $this->user_data['email'])->first();

        $this->assertContains($userUpdated->id . '.jpg', $userUpdated->photo);
    }

    /* Авторизуємося під уже існуючим користувачем, змінуємо атрибут і перевіряємо успішність зміни */
    public function testLoginExistingUserWithUpdate() {
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
    public function testLoginNonExistingUser() {
        // видаляємо користувача з БД. Віднині $user у нас -- неіснуючий
        $this->user->delete();

        $response = $this->post($this->url, $this->getDataToPost(), $this->headers);

        $response->assertStatus(201);
        $this->assertToken($response);

        $user_inserted = User::where('email', $this->user_data['email'])->first();
        $this->assertTrue(!empty($user_inserted));
        $this->assertContains($user_inserted->id . '.jpg', $user_inserted->photo);
        $this->assertEquals($user_inserted->usertype, 2);
    }

    /**
     * Перевіряємо, щоб система видавала помилку при завеликій або замалій довжині атрибутів
     *
     * @param $key
     * @param $min_len
     * @param $max_len
     *
     * @dataProvider providerLoginForValidationFail
     */
    public function testLoginForValidationFail($key, $min_len, $max_len) {
        $data = $this->getDataToPost();

        // Перевіряємо, що при замалій довжині атрибута отримаємо помилку
        $data[$key] = str_repeat('1', $min_len - 1);
        $this->post($this->url, $data, $this->headers)->assertStatus(422);

        // Перевіряємо, що при завеликій довжині атрибута отримаємо помилку
        $data[$key] = str_repeat('1', $max_len + 1);
        $this->post($this->url, $data, $this->headers)->assertStatus(422);
    }

    /**
     * @return array
     */
    public function providerLoginForValidationFail() {
        return [
            ['id', 12, 25],
            ['name', 3, 255],
            ['surname', 3, 255],
        ];
    }

    public function setUp() {
        $this->user_data['google_id'] = '111483939504700006800';

        parent::setUp();

        $this->url .= 'login/google';
    }

    private function getDataToPost()
    {
        return [
            'id'      => $this->user_data['google_id'],
            'email'   => $this->user_data['email'],
            'name'    => $this->user_data['name'],
            'surname' => $this->user_data['surname'],
            'link'    => $this->google_link,
            'picture' => $this->google_picture,
        ];
    }
}
