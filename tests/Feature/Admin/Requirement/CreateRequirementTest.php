<?php

namespace Tests\Feature\Admin\Requirement;

use App\Models\Requirement;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class CreateRequirementTest extends TestCaseWithCourse {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['content' => 'Mindestanforderung 1', 'mandatory' => '1', 'blocks' => null];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayRequirement() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['content']);
    }

    public function test_shouldValidateNewRequirementData_noAnforderungText() {
        // given
        $payload = $this->payload;
        unset($payload['content']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Anforderung muss ausgefüllt sein.', $exception->validator->errors()->first('content'));
    }

    public function test_shouldValidateNewRequirementData_longAnforderungText() {
        // given
        $payload = $this->payload;
        $payload['content'] = ' Die TN kennen den Ablauf der Lagerplanung, verfügen über Werkzeuge der einzelnen Planungsschritte und können ein Lager administrieren. Sie verfügen über vertiefte Kenntnisse der Pfadigrundlagen und können damit ausgewogene Lagerprogramme sowie Blöcke (LA/LS) planen, durchführen und auswerten.';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Anforderung darf maximal 255 Zeichen haben.', $exception->validator->errors()->first('content'));
    }

    public function test_shouldValidateNewRequirementData_mandatoryNotSet_shouldWork() {
        // given
        $payload = $this->payload;
        unset($payload['mandatory']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('>Nein<');
        $response->assertDontSee('>Ja<');
    }

    public function test_shouldValidateNewRequirementData_mandatoryFalse_shouldWork() {
        // given
        $payload = $this->payload;
        $payload['mandatory'] = '0';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('>Nein<');
        $response->assertDontSee('>Ja<');
    }

    public function test_shouldValidateNewRequirementData_mandatoryTrue_shouldWork() {
        // given
        $payload = $this->payload;
        $payload['mandatory'] = '1';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('>Ja<');
        $response->assertDontSee('>Nein<');
    }

    public function test_shouldValidateNewRequirementData_noBlockIds() {
        // given
        $payload = $this->payload;
        $payload['blocks'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement');
        $this->assertEquals([], Requirement::latest()->first()->blocks->pluck('id')->all());
    }

    public function test_shouldValidateNewRequirementData_invalidBlockIds() {
        // given
        $payload = $this->payload;
        $payload['blocks'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Blöcke Format ist ungültig.', $exception->validator->errors()->first('blocks'));
    }

    public function test_shouldValidateNewRequirementData_oneValidRequirementId() {
        // given
        $payload = $this->payload;
        $blockId = $this->createBlock();
        $payload['blocks'] = $blockId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement');
        $this->assertEquals([$blockId], Requirement::latest()->first()->blocks->pluck('id')->all());
    }

    public function test_shouldValidateNewRequirementData_multipleValidBlockIds() {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), $this->createBlock()];
        $payload['blocks'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/requirement');
        $this->assertEquals($blockIds, Requirement::latest()->first()->blocks->pluck('id')->all());
    }

    public function test_shouldValidateNewRequirementData_someNonexistentBlockIds() {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), '999999', $this->createBlock()];
        $payload['blocks'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Blöcke ist ungültig.', $exception->validator->errors()->first('blocks'));
    }

    public function test_shouldValidateNewRequirementData_someInvalidBlockIds() {
        // given
        $payload = $this->payload;
        $blockIds = [$this->createBlock(), 'abc', $this->createBlock()];
        $payload['blocks'] = implode(',', $blockIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/requirement', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Blöcke Format ist ungültig.', $exception->validator->errors()->first('blocks'));
    }

    public function test_shouldShowMessage_whenNoRequirementInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/requirement');

        // then
        $response->assertStatus(200);
        $response->assertSee('Bisher sind keine Anforderungen erfasst.');
    }

    public function test_shouldNotShowMessage_whenSomeRequirementInCourse() {
        // given
        $this->createRequirement();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/requirement');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Bisher sind keine Anforderungen erfasst.');
    }
}
