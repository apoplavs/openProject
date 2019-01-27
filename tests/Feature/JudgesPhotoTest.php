<?php

namespace Tests\Feature;

class JudgesPhotoTest extends BaseApiTest
{
    public function setUp()
    {
        parent::setUp();
        $this->url .= 'judges/photo';
    }

    public function testTrivial()
    {
        $response = $this->post($this->url, [], $this->headersWithToken($this->login($this->user_data)));
        $this->assertCorrectResponse($response);
    }
}