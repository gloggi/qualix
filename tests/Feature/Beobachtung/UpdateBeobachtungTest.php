<?php

namespace Tests\Feature\Beobachtung;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithBasicData;

class UpdateBeobachtungTest extends TestCaseWithBasicData {

    private $beobachtungId;
    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->beobachtungId = $this->createBeobachtung('hat gut mitgemacht', 1, [], [], $this->blockId);

        $blockId2 = $this->createBlock();
        $maId = $this->createMA('Mindestanforderung 1', true);
        $qkId = $this->createQK('Qualikategorie 1');

        $this->payload = ['tn_id' => '' . $this->tnId, 'kommentar' => 'kein Wort gesagt', 'bewertung' => '0', 'block_id' => '' . $blockId2, 'ma_ids' => '' . $maId, 'qk_ids' => '' . $qkId];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/' . $this->beobachtungId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateBeobachtung() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/' . $this->beobachtungId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/tn/' . $this->tnId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['kommentar']);
        $response->assertDontSee('hat gut mitgemacht');
    }

    public function test_shouldValidateNewBeobachtungData_noKommentar() {
        // given
        $payload = $this->payload;
        unset($payload['kommentar']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/' . $this->beobachtungId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_noBewertung() {
        // given
        $payload = $this->payload;
        unset($payload['bewertung']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/' . $this->beobachtungId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidBewertung() {
        // given
        $payload = $this->payload;
        $payload['bewertung'] = '3';

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/' . $this->beobachtungId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_noBlockId() {
        // given
        $payload = $this->payload;
        unset($payload['block_id']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/' . $this->beobachtungId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidBlockId() {
        // given
        $payload = $this->payload;
        $payload['block_id'] = '*';

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/' . $this->beobachtungId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidMAIds() {
        // given
        $payload = $this->payload;
        $payload['ma_ids'] = 'xyz';

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/' . $this->beobachtungId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidQKIds() {
        // given
        $payload = $this->payload;
        $payload['qk_ids'] = 'xyz';

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/' . $this->beobachtungId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateUpdatedBeobachtungURL_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/' . ($this->beobachtungId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
