<?php

namespace Feature\Admin\Block;

use App\Models\Block;
use Carbon\Carbon;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class GenerateBlockTest extends TestCaseWithCourse {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['name' => 'SonstigesBlockGenTest', 'blocks_startdate' => '1.10.2023', 'blocks_enddate' => '7.10.2023', 'requirements' => null];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/generate', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldGenerateAndDisplayOneBlock() {
        // given
        $payload = $this->payload;
        $payload['blocks_enddate'] = $payload['blocks_startdate'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/generate', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($payload['name']);
        $response->assertSee($payload['blocks_startdate']);
    }

    public function test_shouldGenerateAndDisplayBlocks() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/generate', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['name']);
        $response->assertSee($this->payload['blocks_startdate']);
        $response->assertSee($this->payload['blocks_enddate']);
    }

    public function test_shouldGenerateAndDisplayBlocksWithRequirements() {
        // given
        $payload = $this->payload;
        $payload = $this->payload;
        $requirementIds = [$this->createRequirement(), 'abc', $this->createRequirement()];
        $payload['requirements'] = implode(',', $requirementIds);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/generate', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Anforderungen Format ist ungültig.', $exception->validator->errors()->first('requirements'));
    }

    public function test_shouldValidateNewBlockData_invalidRequirements() {
        // given
        $payload = $this->payload;
        $payload['name'] = ' ';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/generate', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Blockname muss ausgefüllt sein.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldNotCreateBlockWithNegativeTimespan() {
        // given
        $payload = $this->payload;
        $payload['name'] = 'NegSonstigesBlock';
        $payload['blocks_startdate'] = '02.10.2023';
        $payload['blocks_enddate'] = '01.10.2023';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/generate', $payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Es wurden keine Blöcke generiert.');
        $response->assertDontSee($this->payload['name']);
    }

    public function test_shouldValidateNewBlockData_noBlockName() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/generate', $payload);

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
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/generate', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Blockname darf maximal 255 Zeichen haben.', $exception->validator->errors()->first('name'));
    }

    public function test_shouldValidateNewBlockData_invalidDate() {
        // given
        $payload = $this->payload;
        $payload['blocks_startdate'] = 'asdf';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks/generate', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals('Startdatum muss ein gültiges Datum sein.', $exception->validator->errors()->first('blocks_startdate'));
    }
}


