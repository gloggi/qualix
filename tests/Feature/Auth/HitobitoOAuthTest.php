<?php

namespace Tests\Feature\Auth;

use App\Models\HitobitoUser;
use App\Models\NativeUser;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class HitobitoOAuthTest extends TestCase {

    public function setUp(): void {
        parent::setUp();
        auth()->logout();
    }

    public function test_loginWithMiData_shouldRedirectToCorrectOAuthUrl() {
        // given

        // when
        $response = $this->get('/login/hitobito');

        // then
        $response->assertRedirect();
        $location = $response->headers->get('Location');
        $this->assertStringStartsWith(
            'https://demo.hitobito.ch/oauth/authorize?client_id=xxx&redirect_uri=https%3A%2F%2Flocalhost%2Flogin%2Fhitobito%2Fcallback&scope=name&response_type=code&state=',
            $location);
    }

    protected function extractRedirectQueryParams($response) {
        $response->assertRedirect();
        parse_str( parse_url( $response->headers->get('Location'), PHP_URL_QUERY), $result );
        return $result;
    }

    protected function mockHitobitoResponses($email, $nickname) {
        $hitobitoMock = new MockHandler([
            // Respond to the authorization_token request
            new Response(200, [], '{"access_token": "abcd"}'),
            // Respond to the profile request
            new Response(200, [], '{"email": "' . $email . '", "nickname": "' . $nickname . '"}'),
        ]);
        config()->set('services.hitobito.guzzle.handler', $hitobitoMock);
    }

    public function test_registerWithMiData_shouldWork() {
        // given
        $this->mockHitobitoResponses('test@hitobito.com', 'Test');
        $this->withSession(['url.intended' => '/some/redirect']);

        // when
        $state = $this->extractRedirectQueryParams($this->get('/login/hitobito'))['state'];
        $response = $this->get('/login/hitobito/callback?code=1234&state=' . $state);

        // then
        $response->assertRedirect('/some/redirect');
        $this->assertAuthenticatedAs(HitobitoUser::first());
    }

    public function test_loginWithMiData_shouldWork() {
        // given
        $email = 'cosinus@hitobito.com';
        $existingUser = factory(HitobitoUser::class)->create(['name' => 'Cosinus', 'email' => $email]);
        $this->mockHitobitoResponses($email, 'Cosinuss');
        $this->withSession(['url.intended' => '/some/redirect']);

        // when
        $state = $this->extractRedirectQueryParams($this->get('/login/hitobito'))['state'];
        $response = $this->get('/login/hitobito/callback?code=1234&state=' . $state);

        // then
        $response->assertRedirect('/some/redirect');
        $this->assertAuthenticatedAs($existingUser);
    }

    public function test_registerWithMiData_shouldFail_whenStateDoesNotMatch() {
        // given
        $this->mockHitobitoResponses('cosinus@hitobito.com', 'Cosinus');

        // when
        $this->get('/login/hitobito');
        $response = $this->get('/login/hitobito/callback?code=1234&state=xxxyyy');

        // then
        $response->assertRedirect('/login');
        $response->followRedirects()->assertSee('Etwas hat nicht geklappt. Versuche es noch einmal.');
    }

    public function test_registerWithMiData_shouldFail_whenHitobitoReportsAnError() {
        // given

        // when
        $state = $this->extractRedirectQueryParams($this->get('/login/hitobito'))['state'];
        $response = $this->get('/login/hitobito/callback?error=access_denied&error_description=Der+Resource+Owner+oder+der+Autorisierungs-Server+hat+die+Anfrage+verweigert.&state=' . $state);

        // then
        $response->assertRedirect('/login');
        $response->followRedirects()->assertSee('Zugriff in MiData verweigert.');
    }

    public function test_registerOrLoginWithMiData_shouldFail_whenEmailBelongsToNativeUser() {
        // given
        $email = 'cosinus@qualix.flamberg.ch';
        factory(NativeUser::class)->create(['name' => 'Cosinus', 'email' => $email]);
        $this->mockHitobitoResponses($email, 'Cosinus');
        $this->withSession(['url.intended' => '/some/redirect']);

        // when
        $state = $this->extractRedirectQueryParams($this->get('/login/hitobito'))['state'];
        $response = $this->get('/login/hitobito/callback?code=1234&state=' . $state);

        // then
        $response->assertRedirect('/login');
        $response->followRedirects()->assertSee('Melde dich bitte wie üblich mit Benutzernamen und Passwort an.');
    }

    public function test_nativeLogin_shouldFail_whenEmailBelongsToHitobitoUser() {
        // given
        $email = 'cosinus@hitobito.com';
        factory(HitobitoUser::class)->create(['name' => 'Cosinus', 'email' => $email]);
        $this->get('/login');

        // when
        $response = $this->post('/login', ['email' => $email, 'password' => '12345678']);

        // then
        $response->assertRedirect('/login');
        $response->followRedirects()->assertSee('Dieses Login ist uns nicht bekannt. Meldest du dich vielleicht normalerweise mit MiData an?');
    }

    public function test_passwordReset_shouldFail_whenEmailBelongsToHitobitoUser() {
        // given
        $email = 'cosinus@hitobito.com';
        factory(HitobitoUser::class)->create(['name' => 'Cosinus', 'email' => $email]);
        $this->get('/password/reset');

        // when
        $response = $this->post('/password/email', ['email' => $email]);

        // then
        $response->assertRedirect('/password/reset');
        $response->followRedirects()->assertSee('Wir können keinen Benutzer mit dieser E-Mail-Adresse finden. Meldest du dich vielleicht normalerweise mit MiData an?');
    }

    public function test_nativeLogin_shouldFail_whenHitobitoIsDown() {
        // given
        // Respond with error 500
        $hitobitoMock = new MockHandler([ new Response(500) ]);
        config()->set('services.hitobito.guzzle.handler', $hitobitoMock);

        // when
        $state = $this->extractRedirectQueryParams($this->get('/login/hitobito'))['state'];
        $response = $this->get('/login/hitobito/callback?code=1234&state=' . $state);

        // then
        $response->assertRedirect('/login');
        $response->followRedirects()->assertSee('Leider klappt es momentan gerade nicht. Versuche es später wieder, oder registriere unten einen klassischen Account.');
    }
}
