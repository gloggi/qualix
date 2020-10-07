<?php

namespace Tests\Feature\Admin\Quali;

use App\Models\Quali;
use Illuminate\Testing\TestResponse;
use Tests\TestCaseWithBasicData;

class DeleteQualiTest extends TestCaseWithBasicData {

    private $qualiDataId;

    public function setUp(): void {
        parent::setUp();

        $quali = Quali::find($this->createQuali('Zwischenquali'));
        $this->qualiDataId = $quali->quali_data->id;
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/qualis/' . $this->qualiDataId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDeleteQuali() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/qualis/' . $this->qualiDataId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/qualis');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Das Quali "Zwischenquali" wurde erfolgreich gelöscht.');

        $response = $this->get('/course/' . $this->courseId . '/admin/qualis');
        $response->assertDontSee('Zwischenquali');
    }

    public function test_shouldValidateDeletedQualiUrl_wrongId() {
        // given

        // when
        $response = $this->delete('/course/' . $this->courseId . '/admin/qualis/' . ($this->qualiDataId + 1));

        // then
        $response->assertStatus(404);
    }
}
