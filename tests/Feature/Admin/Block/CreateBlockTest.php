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

        $this->payload = ['full_block_number' => '1.1', 'name' => 'Block 1', 'block_date' => '01.01.2019', 'requirement_ids' => null];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/bloecke', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldCreateAndDisplayBlock() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/bloecke', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/bloecke');
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
        $response = $this->post('/kurs/' . $this->courseId . '/admin/bloecke', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBlockData_noBlockname() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/bloecke', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBlockData_noDatum() {
        // given
        $payload = $this->payload;
        unset($payload['block_date']);

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/bloecke', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBlockData_invalidDatum() {
        // given
        $payload = $this->payload;
        $payload['block_date'] = 'abc';

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/bloecke', $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldShowMessage_whenNoBloeckeInCourse() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/admin/bloecke');

        // then
        $response->assertStatus(200);
        $response->assertSee('Bisher sind keine Blöcke erfasst.');
    }

    public function test_shouldNotShowMessage_whenSomeBlockInCourse() {
        // given
        $this->createBlock();

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/admin/bloecke');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('Bisher sind keine Blöcke erfasst.');
    }

    public function test_shouldShowTodayInForm_whenNoBlockHasYetBeenCreated() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/admin/bloecke');

        // then
        $response->assertStatus(200);
        $response->assertSee(Carbon::today()->format('d.m.Y'));
    }

    public function test_shouldShowDateFromLastCreatedBlockInForm_whenBlockHasBeenCreated() {
        // given

        // when
        $this->post('/kurs/' . $this->courseId . '/admin/bloecke', $this->payload);
        $response = $this->get('/kurs/' . $this->courseId . '/admin/bloecke');

        // then
        $response->assertStatus(200);
        $response->assertDontSee(Carbon::today()->format('d.m.Y'));
        $this->assertRegExp('/<date-picker.*value="' . str_replace('.', '\.', $this->payload['block_date']) . '"/s', $response->content());
    }
}
