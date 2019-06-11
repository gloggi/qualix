<?php

namespace Tests\Feature\Observation;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithBasicData;

class CreateObservationTest extends TestCaseWithBasicData {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['participant_ids' => '' . $this->participantId, 'content' => 'hat gut mitgemacht', 'impression' => '1', 'block_id' => '' . $this->blockId, 'requirement_ids' => '', 'category_ids' => ''];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/overview/new', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayBeobachtung() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/overview/new', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/overview/new?tn=' . $this->participantId . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beobachtung erfasst.');
    }

    public function test_shouldValidateNewBeobachtungData_noTNIds() {
        // given
        $payload = $this->payload;
        unset($payload['participant_ids']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/overview/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidTNIds() {
        // given
        $payload = $this->payload;
        $payload['participant_ids'] = 'a';

        // when
        $response = $this->post('/course/' . $this->courseId . '/overview/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_multipleTNIds_shouldWork() {
        // given
        $tnId2 = $this->createParticipant('Pfnörch');
        $tnIds = $this->participantId . ',' . $tnId2;
        $payload = $this->payload;
        $payload['participant_ids'] = $tnIds;

        // when
        $response = $this->post('/course/' . $this->courseId . '/overview/new', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/overview/new?tn=' . urlencode($tnIds) . '&block=' . $this->blockId);
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beobachtungen erfasst.');
    }

    public function test_shouldValidateNewBeobachtungData_noKommentar() {
        // given
        $payload = $this->payload;
        unset($payload['content']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/overview/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_noImpression() {
        // given
        $payload = $this->payload;
        unset($payload['impression']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/overview/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidImpression() {
        // given
        $payload = $this->payload;
        $payload['impression'] = '3';

        // when
        $response = $this->post('/course/' . $this->courseId . '/overview/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_noBlockId() {
        // given
        $payload = $this->payload;
        unset($payload['block_id']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/overview/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidBlockId() {
        // given
        $payload = $this->payload;
        $payload['block_id'] = '*';

        // when
        $response = $this->post('/course/' . $this->courseId . '/overview/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidMAIds() {
        // given
        $payload = $this->payload;
        $payload['requirement_ids'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/overview/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBeobachtungData_invalidCategoryIds() {
        // given
        $payload = $this->payload;
        $payload['category_ids'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/overview/new', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }
}
