<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Toecyd\PasswordReset;
use Toecyd\User;

class ResetPasswordCreateTest extends BaseApiTest
{
    private $params = [];

    public function setUp()
    {
        parent::setUp();
        $this->url .= 'user/password/new';

        $this->params = [
            'token' => str_random(60),
            'password' => '1234AbCd',
        ];

        (new PasswordReset([
            'token' => $this->params['token'],
            'email' => $this->user->email,
        ]))->save();

    }

    public function testSuccess()
    {
        $response = $this->post($this->url, $this->params, []);
        $this->assertEquals(200, $response->status(), $response->getContent());

        $this->assertTrue(Auth::attempt([
            'email' => $this->user->email,
            'password' => $this->params['password']
        ]));

        $this->assertEmpty(PasswordReset::where('token', $this->params['token'])->first());
    }

    public function testErrorNonExistingToken()
    {
        $this->params['token'] = 'non_existing_value';
        $response = $this->post($this->url, $this->params, []);
        $response->assertStatus(302);
    }
}
