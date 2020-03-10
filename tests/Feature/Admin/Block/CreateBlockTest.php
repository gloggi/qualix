<?php

namespace Tests\Feature\Admin\Block;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithCourse;

class CreateBlockTest extends TestCaseWithCourse {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $this->payload = ['full_block_number' => '1.1', 'name' => 'Block 1', 'block_date' => '01.01.2019', 'requirements' => null];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayBlock() {
        // given

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/blocks');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee($this->payload['full_block_number']);
        $response->assertSee($this->payload['name']);
        $response->assertSee($this->payload['block_date']);
    }

    public function test_shouldValidateNewBlockData_invalidFullBlockNumber() {
        // given
        $payload = $this->payload;
        $payload['full_block_number'] = 'abc';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBlockData_noBlockname() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBlockData_longBlockname() {
        // given
        $payload = $this->payload;
        $payload['name'] = 'Extrem langer Blockname 1Extrem langer Blockname 2Extrem langer Blockname 3Extrem langer Blockname 4Extrem langer Blockname 5Extrem langer Blockname 6Extrem langer Blockname 7Extrem langer Blockname 8Extrem langer Blockname 9Extrem langer Blockname 10Extrem langer Blockname 11';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBlockData_noDatum() {
        // given
        $payload = $this->payload;
        unset($payload['block_date']);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBlockData_invalidDatum() {
        // given
        $payload = $this->payload;
        $payload['block_date'] = 'abc';

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/blocks', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldShowMessage_whenNoBlocksInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/blocks');

        // then
        $response->assertStatus(200);
        $response->assertSee('Bisher sind keine Blöcke erfasst.');
    }

    public function test_shouldNotShowMessage_whenSomeBlockInCourse() {
        // given
        $this->createBlock();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/blocks');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Bisher sind keine Blöcke erfasst.');
    }

    public function test_shouldShowTodayInForm_whenNoBlockHasYetBeenCreated() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/blocks');

        // then
        $response->assertStatus(200);
        $response->assertSee(Carbon::today()->format('d.m.Y'));
    }

    public function test_shouldShowDateFromLastCreatedBlockInForm_whenBlockHasBeenCreated() {
        // given

        // when
        $this->post('/course/' . $this->courseId . '/admin/blocks', $this->payload);
        $response = $this->get('/course/' . $this->courseId . '/admin/blocks');

        // then
        $response->assertStatus(200);
        $response->assertDontSee(Carbon::today()->format('d.m.Y'));
        $this->assertRegExp('/<date-picker.*value="' . str_replace('.', '\.', $this->payload['block_date']) . '"/s', $response->content());
    }
}
