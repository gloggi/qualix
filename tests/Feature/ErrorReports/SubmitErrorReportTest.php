<?php

namespace Tests\Feature\Auth;

use GuzzleHttp;
use Tests\TestCase;

class SubmitErrorReportTest extends TestCase {

    public function test_shouldSubmitErrorReport() {
        // given
        $this->mock(GuzzleHttp\Client::class, function ($mock) {
            $mock->shouldReceive('post')->with('https://sentry.io/api/0/projects/xyz/xyz/user-feedback/', [
                'headers' => ['Authorization' => 'DSN https://123412341234123412341234@sentry.io/12345'],
                'form_params' => [
                    'event_id' => '1234',
                    'name' => 'Bari',
                    'email' => 'email@email.com',
                    'comments' => 'Ich habe so ein bitzli im Qualix herumgeklickt, und dann BÄM!',
                ]
            ]);
        });

        // when
        $response = $this->post('/error-report', [
            'eventId' => '1234',
            'previousUrl' => 'https://previous.com',
            'name' => 'Bari',
            'email' => 'email@email.com',
            'description' => 'Ich habe so ein bitzli im Qualix herumgeklickt, und dann BÄM!',
        ]);

        // then
        $response->assertStatus(302);
        $response->assertLocation('/error-report');
        $response = $response->followRedirects();
        $response->assertSeeText('Deine Beschreibung wurde abgesendet. Vielen Dank!');
    }
}
