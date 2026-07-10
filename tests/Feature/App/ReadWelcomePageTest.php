<?php

namespace Tests\Feature\App;

use Illuminate\Support\Facades\Config;
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

    public function test_shouldLinkGermanChangelog_whenLocaleGerman() {
        // given
        $this->withSession(['locale' => 'de']);

        // when
        $response = $this->get('/course/' . $this->courseId);

        // then
        $response->assertOk();
        $response->assertSee('href="https://github.com/gloggi/qualix/blob/master/CHANGELOG.md#changelog"', false);
    }

    public function test_shouldLinkFrenchChangelog_whenLocaleFrench() {
        // given
        $this->withSession(['locale' => 'fr']);

        // when
        $response = $this->get('/course/' . $this->courseId);

        // then
        $response->assertOk();
        $response->assertSee('href="https://github.com/gloggi/qualix/blob/master/CHANGELOG_fr.md#journal-des-modifications"', false);
    }

    public function test_shouldDisplayContactWhenContactLinkSet(){
        // given
        Config::set('app.contact.link', 'mailto:test@test.ch');
        // when
        $response = $this->get('/course/' . $this->courseId);
        //then
        $response->assertOk();
        $response->assertSee(env('APP_CONTACT_TEXT', __('t.footer.contact_text')));
    }

    public function test_shouldNotDisplayContactWhenContactLinkNotSet(){
        // given
        Config::set('app.contact.link', '');
        // when
        $response = $this->get('/course/' . $this->courseId);
        //then
        $response->assertOk();
        $response->assertDontSee(env('APP_CONTACT_TEXT', __('t.footer.contact_text')));
    }
}
