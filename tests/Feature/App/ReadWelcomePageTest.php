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

    public function test_shouldDisplayContactWhenContactLinkSet(){
        // given
        putenv("APP_CONTACT_LINK=mailto:test@test.ch");
        // when
        $response = $this->get('/course/' . $this->courseId);
        //then
        $response->assertOk();
        $response->assertSee(env('APP_CONTACT_TEXT', __('t.footer.contact_text')));
    }
    public function test_shouldNotDisplayContactWhenContactLinkNotSet(){
        // given
        putenv("APP_CONTACT_LINK=");
        // when
        $response = $this->get('/course/' . $this->courseId);
        //then
        $response->assertOk();
        $response->assertDontSee(env('APP_CONTACT_TEXT', __('t.footer.contact_text')));
    }
}
