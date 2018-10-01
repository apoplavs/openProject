<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Toecyd\User;

class LoginGoogleTest extends TestCase
{
    use DatabaseTransactions;

    private $testUsers = [];
    private $url = 'api/v1/login/google';
    private $googleLink = 'https://plus.google.com/111483939504700006800';
    private $googlePicture = 'https://lh3.googleusercontent.com/-LC0h1Ai3sXg/W6XiqkKewLI/AAAAAAAAAMQ/olvDX7mRLlwgDnzE9ARggY3dXaNu7Rh-ACJkCGAYYCw/w1024-h576-n-rw-no/my-photo.jpg';

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
            'id' => $user->google_id,
            'email' => $user->email,
            'name' => $user->name,
            'surname' => $user->surname,
            'link' => $this->googleLink,
            'picture' => $this->googlePicture,
        ];
        $response = $this->post($this->url, $data, $this->headers);

        $response->assertStatus(200);
        $this->assertNotEmpty($response->decodeResponseJson()['token']);

        $userUpdated = User::where('email', $user->email)->first();

        $this->assertContains($userUpdated->id . '.jpg', $userUpdated->photo);
        $this->assertNotEmpty($userUpdated->remember_token);
    }

    public function testLoginExistingUserWithUpdate()
    {
        $user = $this->testUsers[0];

        $data = [
            'id' => $user->google_id,
            'email' => $user->email,
            'name' => $user->name,
            'surname' => $user->surname . '_updated',
            'link' => $this->googleLink,
            'picture' => $this->googlePicture,
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

        // удаляем пользователей из базы. Отныне $user у нас -- несуществующий
        $this->deleteTestUsers();

        $data = [
            'id' => $user->google_id,
            'email' => $user->email,
            'name' => $user->name,
            'surname' => $user->surname,
            'link' => $this->googleLink,
            'picture' => $this->googlePicture,
        ];
        $response = $this->post($this->url, $data, $this->headers);

        $response->assertStatus(201);
        $this->assertNotEmpty($response->decodeResponseJson()['token']);

        $userInserted = User::where('email', $user->email)->first();
        $this->assertTrue(!empty($userInserted));
        $this->assertContains($userInserted->id . '.jpg', $userInserted->photo);
        $this->assertEquals($userInserted->usertype, 2);
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
            'google_id' => '111483939504700006800',
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
