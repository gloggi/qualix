<?php

namespace Tests\Feature\Admin\Block;

use App\Models\Block;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class UpdateBlockTest extends TestCaseWithCourse {

    private $payload;
    private $blockId;

    public function setUp(): void {
        parent::setUp();

        $this->blockId = $this->createBlock('Block 1');

        $this->payload = ['full_block_number' => '1.2', 'name' => 'Geänderter Blockname', 'block_date' => '22.12.2019', 'requirements' => null];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateBlock() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['full_block_number']);
        $response->assertSee($this->payload['name']);
        $response->assertSee($this->payload['block_date']);
        $response->assertDontSee('1.1');
        $response->assertDontSee('Block 1');
        $response->assertDontSee('01.01.2019');
    }

    public function test_shouldValidateNewBlockData_invalidFullBlockNumber() {
        // given
        $payload = $this->payload;
        $payload['full_block_number'] = 'abc';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Blocknummer Format ist ungültig.', $exception->validator->errors()->first('full_block_number'));
    }

    public function test_shouldValidateNewBlockData_noBlockname() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Blockname muss ausgefüllt sein.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewBlockData_longBlockname() {
        // given
        $payload = $this->payload;
        $payload['name'] = 'Extrem langer Blockname 1Extrem langer Blockname 2Extrem langer Blockname 3Extrem langer Blockname 4Extrem langer Blockname 5Extrem langer Blockname 6Extrem langer Blockname 7Extrem langer Blockname 8Extrem langer Blockname 9Extrem langer Blockname 10Extrem langer Blockname 11';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Blockname darf maximal 255 Zeichen haben.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewBlockData_noDate() {
        // given
        $payload = $this->payload;
        unset($payload['block_date']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Datum muss ausgefüllt sein.', $exception->validator->errors()->first('block_date'));
    }

    public function test_shouldValidateNewBlockData_invalidDate() {
        // given
        $payload = $this->payload;
        $payload['block_date'] = 'abc';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Datum muss ein gültiges Datum sein.', $exception->validator->errors()->first('block_date'));
    }

    public function test_shouldValidateNewBlockData_noRequirementIds() {
        // given
        $payload = $this->payload;
        $payload['requirements'] = null;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        $this->assertEquals([], Block::latest()->first()->requirements->pluck('id')->all());
    }

    public function test_shouldValidateNewBlockData_invalidRequirementIds() {
        // given
        $payload = $this->payload;
        $payload['requirements'] = 'xyz';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Anforderungen Format ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewBlockData_oneValidRequirementId() {
        // given
        $payload = $this->payload;
        $requirementId = $this->createRequirement();
        $payload['requirements'] = $requirementId;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        $this->assertEquals([$requirementId], Block::latest()->first()->requirements->pluck('id')->all());
    }

    public function test_shouldValidateNewBlockData_multipleValidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId, $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        $this->assertEquals($requirementIds, Block::latest()->first()->requirements->pluck('id')->all());
    }

    public function test_shouldValidateNewBlockData_someNonexistentRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), '999999', $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Der gewählte Wert für Anforderungen ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewBlockData_someInvalidRequirementIds() {
        // given
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), 'abc', $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . $this->blockId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Anforderungen Format ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewBlockData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/' . ($this->blockId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
