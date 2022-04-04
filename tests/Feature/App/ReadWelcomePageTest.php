<?php

namespace Tests\Feature\App;

use Tests\TestCaseWithBasicData;

class ReadWelcomePageTest extends TestCaseWithBasicData {

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayWelcomePage() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId);

        // then
        $response->assertOk();
        $response->assertSee('Willkommä bim Qualix');
    }

    public function test_shouldDisplayContact(){
        // given

        // when
        $response = $this->get('/course/' . $this->courseId);
        //then
        $response->assertOk();
        $response->assertSee(env('APP_CONTACT_TEXT'));
    }
}
