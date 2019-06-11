<?php

namespace Tests\Feature\Beobachtung;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithBasicData;

class UpdateBeobachtungTest extends TestCaseWithBasicData {

    private $observationId;
    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->observationId = $this->createBeobachtung('hat gut mitgemacht', 1, [], [], $this->blockId);

        $blockId2 = $this->createBlock();
        $maId = $this->createMA('Mindestanforderung 1', true);
        $categoryId = $this->createCategory('Kategorie 1');

        $this->payload = ['participant_id' => '' . $this->participantId, 'content' => 'kein Wort gesagt', 'impression' => '0', 'block_id' => '' . $blockId2, 'requirement_ids' => '' . $maId, 'category_ids' => '' . $categoryId];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/beobachtungen/' . $this->observationId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateBeobachtung() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/beobachtungen/' . $this->observationId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/tn/' . $this->participantId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['content']);
        $response->assertDontSee('hat gut mitgemacht');
    }

    public function test_shouldValidateNewBeobachtungData_noKommentar() {
        // given
        $payload = $this->payload;
        unset($payload['content']);

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/beobachtungen/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_noImpression() {
        // given
        $payload = $this->payload;
        unset($payload['impression']);

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/beobachtungen/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidImpression() {
        // given
        $payload = $this->payload;
        $payload['impression'] = '3';

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/beobachtungen/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_noBlockId() {
        // given
        $payload = $this->payload;
        unset($payload['block_id']);

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/beobachtungen/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidBlockId() {
        // given
        $payload = $this->payload;
        $payload['block_id'] = '*';

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/beobachtungen/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidMAIds() {
        // given
        $payload = $this->payload;
        $payload['requirement_ids'] = 'xyz';

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/beobachtungen/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidCategoryIds() {
        // given
        $payload = $this->payload;
        $payload['category_ids'] = 'xyz';

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/beobachtungen/' . $this->observationId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateUpdatedBeobachtungURL_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/beobachtungen/' . ($this->observationId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
