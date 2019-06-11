<?php

namespace Tests\Feature\Admin\Bloecke;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Tests\TestCaseWithKurs;

class UpdateBlockTest extends TestCaseWithKurs {

    private $payload;
    private $blockId;

    public function setUp(): void {
        parent::setUp();

        $this->blockId = $this->createBlock('Block 1');

        $this->payload = ['full_block_number' => '1.2', 'name' => 'Geänderter Blockname', 'block_date' => '22.12.2019', 'requirement_ids' => null];
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/bloecke/' . $this->blockId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldUpdateBlock() {
        // given

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/bloecke/' . $this->blockId, $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/kurs/' . $this->courseId . '/admin/bloecke');
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
        $response = $this->post('/kurs/' . $this->courseId . '/admin/bloecke/' . $this->blockId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBlockData_noBlockname() {
        // given
        $payload = $this->payload;
        unset($payload['name']);

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/bloecke/' . $this->blockId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBlockData_noDatum() {
        // given
        $payload = $this->payload;
        unset($payload['block_date']);

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/bloecke/' . $this->blockId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBlockData_invalidDatum() {
        // given
        $payload = $this->payload;
        $payload['block_date'] = 'abc';

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/bloecke/' . $this->blockId, $payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function test_shouldValidateNewBlockData_wrongId() {
        // given
        $payload = $this->payload;

        // when
        $response = $this->post('/kurs/' . $this->courseId . '/admin/bloecke/' . ($this->blockId + 1), $payload);

        // then
        $response->assertStatus(404);
    }
}
