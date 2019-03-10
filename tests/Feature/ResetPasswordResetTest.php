<?php

namespace Tests\Feature;

use Toecyd\Mail\AccountMail;
use Toecyd\PasswordReset;
use Illuminate\Support\Facades\Mail;

class ResetPasswordResetTest extends BaseApiTest
{
    public function setUp()
    {
        parent::setUp();
        $this->url .= 'user/password/reset';

        Mail::fake();
    }

    public function testSuccess()
    {
        $response = $this->post($this->url, ['email' => $this->user->email], []);
        $this->assertEquals(200, $response->status(), $response->getContent());

        $this->assertEquals(1, PasswordReset::where('email', $this->user->email)->count());
        Mail::assertSent(AccountMail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function testErrorNonExistingEmail()
    {
        $email = 'test@example.com';
        $response = $this->post($this->url, ['email' => $email], []);
        $response->assertStatus(302);
    }

    public function testErrorRepeatEmail()
    {
        $response = $this->post($this->url, ['email' => $this->user->email], []);
        $this->assertEquals(200, $response->status(), $response->getContent());

        $response = $this->post($this->url, ['email' => $this->user->email], []); // повтор
        $this->assertEquals(302, $response->status()); // отработал валидатор

        $this->assertEquals(1, PasswordReset::where('email', $this->user->email)->count()); // повторный email не записался в базу, но и не удалился из неё
    }
}
