<?php

namespace Tests\Feature\App;

use Illuminate\Support\Facades\App;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class SelectLocaleWhenLoggedInTest extends TestCase {

    protected $supportedLocales = [ 'de', 'fr' ];

    public function test_selectDifferentLocale() {
        // given
        $this->withSession(['locale' => 'de']);
        // Simulate previous request
        $this->get('/user');

        // when
        $response = $this->get('/locale/fr');

        // then
        $response->assertRedirect('/user');
        $response->assertSessionHas('locale', 'fr');

        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertOk();
        $response->assertSessionHas('locale', 'fr');
        $this->assertThat(App::getLocale(), $this->equalTo('fr'));
        $response->assertHeader('Content-Language', 'fr');
    }

    public function test_selectSameLocale() {
        // given
        $this->withSession(['locale' => 'fr']);
        // Simulate previous request
        $this->get('/user');

        // when
        $response = $this->get('/locale/fr');

        // then
        $response->assertRedirect('/user');
        $response->assertSessionHas('locale', 'fr');

        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertOk();
        $response->assertSessionHas('locale', 'fr');
        $this->assertThat(App::getLocale(), $this->equalTo('fr'));
    }

    public function test_shouldUseLocale_german() {
        $this->checkSelectedLocale('de', 'de', 'de');
    }

    public function test_shouldUseLocale_french() {
        $this->checkSelectedLocale('fr', 'fr', 'fr');
    }

    public function test_shouldUseLocaleFromSession_whenSessionAndBrowserDisagree() {
        $this->checkSelectedLocale('de', 'fr', 'fr');
    }

    public function test_shouldFallBackToFirstSupportedLocale_whenSessionLocaleNotSupported() {
        $this->checkSelectedLocale('it', 'it', 'de');
    }

    public function test_shouldFallBackToBrowserLocale_whenSessionLocaleNotSupported_andBrowserIsSet() {
        $this->checkSelectedLocale('fr', 'it', 'fr');
    }

    public function test_shouldUseLocaleFromBrowser_whenNoSession_german() {
        $this->checkSelectedLocale('de', null, 'de');
    }

    public function test_shouldUseLocaleFromBrowser_whenNoSession_french() {
        $this->checkSelectedLocale('fr', null, 'fr');
    }

    public function test_shouldParseAcceptLanguageHeader_whenNoSession() {
        $this->checkSelectedLocale('it-CH, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5', null, 'fr');
    }

    public function test_shouldParseAcceptLanguageHeader_includingQualityFactors_whenNoSession() {
        $this->checkSelectedLocale('it-CH, fr;q=0.7, en;q=0.8, de;q=0.9, *;q=0.5', null, 'de');
    }

    private function checkSelectedLocale($acceptLanguage, $session, $expected) {
        // given
        $unselected = array_diff($this->supportedLocales, [$expected]);
        $this->withSession(['locale' => $session]);
        $headers = ['Accept-Language' => $acceptLanguage];

        // when
        $response = $this->get('/user', [], $headers);

        // then
        $response->assertOk();
        $this->assertSeeAllInOrder('#navbar-locale-select', [$expected], function($domElement) { return $domElement->getAttribute('text'); });
        $this->assertSeeAllInOrder('#navbar-locale-select b-dropdown-item', $unselected);
        $response->assertSessionHas('locale', $expected);
        $this->assertThat(App::getLocale(), $this->equalTo($expected));
        $response->assertHeader('Content-Language', $expected);
    }
}
