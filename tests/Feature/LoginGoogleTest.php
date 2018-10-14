<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Toecyd\User;

class LoginGoogleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var array
     */
    private $test_users = [];

    /**
     * @var string
     */
    private $url = 'api/v1/login/google';

    /**
     * @var string
     */
    private $google_link = 'https://plus.google.com/111483939504700006800';

    /**
     * @var string
     */
    private $google_picture = 'https://lh3.googleusercontent.com/-LC0h1Ai3sXg/W6XiqkKewLI/AAAAAAAAAMQ/olvDX7mRLlwgDnzE9ARggY3dXaNu7Rh-ACJkCGAYYCw/w1024-h576-n-rw-no/my-photo.jpg';

    /**
     * @var array
     */
    private $headers = ['accept' => 'application/json'];

    /**
     *
     */
    public function testLoginWithoutParams() {
        $data = [];
        $response = $this->post($this->url, $data, $this->headers);

        $response->assertStatus(422);
    }

    /**
     *
     */
    public function testLoginExistingUser() {
        $user = $this->test_users[0];

        $data = [
            'id'      => $user->google_id,
            'email'   => $user->email,
            'name'    => $user->name,
            'surname' => $user->surname,
            'link'    => $this->google_link,
            'picture' => $this->google_picture,
        ];
        $response = $this->post($this->url, $data, $this->headers);

        $response->assertStatus(200);
        $this->assertNotEmpty($response->decodeResponseJson()['access_token']);

        $userUpdated = User::where('email', $user->email)->first();

        $this->assertContains($userUpdated->id . '.jpg', $userUpdated->photo);
        $this->assertNotEmpty($userUpdated->remember_token);
    }

    /**
     *
     */
    public function testLoginExistingUserWithUpdate() {
        $user = $this->test_users[0];

        $data = [
            'id'      => $user->google_id,
            'email'   => $user->email,
            'name'    => $user->name,
            'surname' => $user->surname . '_updated',
            'link'    => $this->google_link,
            'picture' => $this->google_picture,
        ];
        $response = $this->post($this->url, $data, $this->headers);

        $response->assertStatus(200);
        $response->assertSee('access_token');

        $userUpdated = User::where('email', $user->email)->first();

        $this->assertContains($userUpdated->id . '.jpg', $userUpdated->photo);
        foreach (['email', 'name', 'surname'] as $key) {
            $this->assertEquals($userUpdated->$key, $data[$key]);
        }
    }

    /**
     *
     */
    public function testLoginNonExistingUser() {
        $user = $this->test_users[0];

        // удаляем пользователей из базы. Отныне $user у нас -- несуществующий
        $this->deleteTestUsers();

        $data = [
            'id'      => $user->google_id,
            'email'   => $user->email,
            'name'    => $user->name,
            'surname' => $user->surname,
            'link'    => $this->google_link,
            'picture' => $this->google_picture,
        ];
        $response = $this->post($this->url, $data, $this->headers);

        $response->assertStatus(201);
        $this->assertNotEmpty($response->decodeResponseJson()['access_token']);

        $user_inserted = User::where('email', $user->email)->first();
        $this->assertTrue(!empty($user_inserted));
        $this->assertContains($user_inserted->id . '.jpg', $user_inserted->photo);
        $this->assertEquals($user_inserted->usertype, 2);
    }

    /**
     * @param $attr_name
     * @param $min_len
     * @param $max_len
     *
     * @dataProvider providerLoginForValidationFail
     */
    public function testLoginForValidationFail($attr_name, $min_len, $max_len) {
        $user = $this->test_users[0];

        $data = [
            'id'      => $user->google_id,
            'email'   => $user->email,
            'name'    => $user->name,
            'surname' => $user->surname,
            'link'    => $this->google_link,
            'picture' => $this->google_picture,
        ];

        // Перевіряю, що при замалій довжині атрибута я отримаю помилку
        $data[$attr_name] = str_repeat('1', $min_len - 1);
        $response = $this->post($this->url, $data, $this->headers);
        $response->assertStatus(422);

        // Перевіряю, що при завеликій довжині атрибута я отримаю помилку
        $data[$attr_name] = str_repeat('1', $max_len + 1);
        $response = $this->post($this->url, $data, $this->headers);
        $response->assertStatus(422);
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

    /**
     *
     */
    public function setUp() {
        parent::setUp();
        $this->insertTestUsers();
    }

    /**
     *
     */
    private function insertTestUsers() {
        $this->test_users[] = factory(User::class)->create([
            'name'      => 'slualexvas_test_name',
            'surname'   => 'slualexvas_test_surname',
            'google_id' => '111483939504700006800',
            'email'     => 'slualexvas@gmail.com',
            'password'  => bcrypt('test_password'),
        ]);
    }

    /**
     *
     */
    private function deleteTestUsers() {
        foreach ($this->test_users as $user) {
            $user->delete();
        }
    }
}
