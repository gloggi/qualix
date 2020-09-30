<?php

namespace Tests\Feature\Admin\Quali;

use App\Models\Quali;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithBasicData;

class ReadQualiTest extends TestCaseWithBasicData {

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
        $response = $this->get('/course/' . $this->courseId . '/admin/qualis/' . $this->qualiDataId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayQualiData() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/qualis/' . $this->qualiDataId);

        // then
        $response->assertOk();
        $response->assertSee('Zwischenquali');
    }

    public function test_shouldNotDisplayQuali_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createCourse('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/qualis/' . $this->qualiDataId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayQuali_fromOtherUser() {
        // given
        $otherCourseId = $this->createCourse('Zweiter Kurs', '', false);
        $otherQualiDataId = Quali::find($this->createQuali('Fremdes Quali', $otherCourseId))->quali_data->id;

        // when
        $response = $this->get('/course/' . $otherCourseId . '/admin/qualis/' . $otherQualiDataId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
