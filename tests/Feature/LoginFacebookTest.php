<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Toecyd\User;

class LoginFacebookTest extends TestCase
{
    use DatabaseTransactions;

    private $testUsers = [];
    private $url = 'api/v1/login/facebook';
    private $headers = ['accept' => 'application/json'];

    public function testLoginWithoutParams()
    {
        $data = [];
        $response = $this->post($this->url, $data, $this->headers);

        $response->assertStatus(422);
    }

    public function testLoginExistingUser()
    {
        $user = $this->testUsers[0];

        $data = [
            'id' => $user->facebook_id,
            'email' => $user->email,
            'name' => $user->name,
            'surname' => $user->surname,
        ];
        $response = $this->post($this->url, $data, $this->headers);

        $response->assertStatus(200);
        $response->assertSee('token');

        $userUpdated = User::where('email', $user->email)->first();

        $this->assertContains($userUpdated->id . '.jpg', $userUpdated->photo);
        $this->assertEquals($data['id'], $userUpdated->facebook_id);
    }

    public function testLoginExistingUserWithUpdate()
    {
        $user = $this->testUsers[0];

        $data = [
            'id' => $user->facebook_id,
            'email' => $user->email,
            'name' => $user->name,
            'surname' => $user->surname,
        ];
        $response = $this->post($this->url, $data, $this->headers);

        $response->assertStatus(200);
        $response->assertSee('token');

        $userUpdated = User::where('email', $user->email)->first();

        $this->assertContains($userUpdated->id . '.jpg', $userUpdated->photo);
        foreach (['email', 'name', 'surname'] as $key) {
            $this->assertEquals($userUpdated->$key, $data[$key]);
        }
    }

    public function testLoginNonExistingUser()
    {
        $user = $this->testUsers[0];

        // Видаляємо користувачів із бази. Відтепер user у нас nonExisting
        $this->deleteTestUsers();

        $data = [
            'id' => $user->facebook_id,
            'email' => $user->email,
            'name' => $user->name,
            'surname' => $user->surname,
        ];
        $response = $this->post($this->url, $data, $this->headers);

        $response->assertStatus(201);
        $response->assertSee('token');

        $userInserted = User::where('email', $user->email)->first();
        $this->assertTrue(!empty($userInserted));
        $this->assertContains($userInserted->id . '.jpg', $userInserted->photo);
        $this->assertEquals(2, $userInserted->usertype);
    }

    public function testLoginWithInvalidFacebookId()
    {
        $user = $this->testUsers[0];

        $data = [
            'id' => $user->facebook_id . '12345',
            'email' => $user->email,
            'name' => $user->name,
            'surname' => $user->surname,
        ];
        $response = $this->post($this->url, $data, $this->headers);

        $response->assertStatus(401);
    }

    public function setUp()
    {
        parent::setUp();
        $this->insertTestUsers();
    }

    private function insertTestUsers()
    {
        $this->testUsers[] = factory(User::class)->create([
            'name' => 'slualexvas_test_name',
            'surname' => 'slualexvas_test_surname',
            'facebook_id' => '100001887847445',
            'email' => 'slualexvas@gmail.com',
            'password' => bcrypt('test_password'),
        ]);
    }

    private function deleteTestUsers()
    {
        foreach ($this->testUsers as $user) {
            $user->delete();
        }
    }
}
