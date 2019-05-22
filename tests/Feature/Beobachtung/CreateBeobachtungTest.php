<?php

namespace Tests\Feature\Beobachtung;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithBasicData;

class CreateBeobachtungTest extends TestCaseWithBasicData {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['tn_ids' => '' . $this->tnId, 'kommentar' => 'hat gut mitgemacht', 'bewertung' => '1', 'block_id' => '' . $this->blockId, 'ma_ids' => '', 'qk_ids' => ''];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayBeobachtung() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/beobachtungen/neu?tn=' . $this->tnId . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beobachtung erfasst.');
    }

    public function test_shouldValidateNewBeobachtungData_noTNIds() {
        // given
        $payload = $this->payload;
        unset($payload['tn_ids']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidTNIds() {
        // given
        $payload = $this->payload;
        $payload['tn_ids'] = 'a';

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_multipleTNIds_shouldWork() {
        // given
        $this->post('/kurs/' . $this->kursId . '/admin/tn', ['pfadiname' => 'Pfnörch']);
        $tnId2 = $this->user()->lastAccessedKurs->tns()->get()[1]->id;
        $tnIds = $this->tnId . ',' . $tnId2;
        $payload = $this->payload;
        $payload['tn_ids'] = $tnIds;

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->kursId . '/beobachtungen/neu?tn=' . urlencode($tnIds) . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beobachtungen erfasst.');
    }

    public function test_shouldValidateNewBeobachtungData_noKommentar() {
        // given
        $payload = $this->payload;
        unset($payload['kommentar']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_noBewertung() {
        // given
        $payload = $this->payload;
        unset($payload['bewertung']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidBewertung() {
        // given
        $payload = $this->payload;
        $payload['bewertung'] = '3';

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_noBlockId() {
        // given
        $payload = $this->payload;
        unset($payload['block_id']);

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidBlockId() {
        // given
        $payload = $this->payload;
        $payload['block_id'] = '*';

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidMAIds() {
        // given
        $payload = $this->payload;
        $payload['ma_ids'] = 'xyz';

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidQKIds() {
        // given
        $payload = $this->payload;
        $payload['qk_ids'] = 'xyz';

        // when
        $response = $this->post('/kurs/' . $this->kursId . '/beobachtungen/neu', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }
}
