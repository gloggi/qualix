<?php

namespace Tests\Feature\Admin\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithKurs;

class ReadCategoryTest extends TestCaseWithKurs {

    private $qkId;

    public function setUp(): void {
        parent::setUp();

        $this->qkId = $this->createCategory('Qualikategorie 1');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/admin/qk/' . $this->qkId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayQK() {
        // given

        // when
        $response = $this->get('/kurs/' . $this->courseId . '/admin/qk/' . $this->qkId);

        // then
        $response->assertOk();
        $response->assertSee('Qualikategorie 1');
    }

    public function test_shouldNotDisplayQK_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '');

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/admin/qk/' . $this->qkId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayQK_fromOtherUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '', false);
        $otherQKId = Category::create(['course_id' => $otherKursId, 'name' => 'Qualikategorie 1'])->id;

        // when
        $response = $this->get('/kurs/' . $otherKursId . '/admin/qk/' . $otherQKId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
