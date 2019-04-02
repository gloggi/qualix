<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testHomeWithoutLogin()
    {
        // given
        auth()->logout();

        // when
        $response = $this->get('/');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * A basic test example with a fake user.
     *
     * @return void
     */
    public function testHomeWithLogin()
    {
        // given

        // when
        $response = $this->get('/');

        // then
        $response->assertStatus(200);
    }
}
