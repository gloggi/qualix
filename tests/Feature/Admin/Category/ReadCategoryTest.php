<?php

namespace Tests\Feature\Admin\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCaseWithCourse;

class ReadCategoryTest extends TestCaseWithCourse {

    private $categoryId;

    public function setUp(): void {
        parent::setUp();

        $this->categoryId = $this->createCategory('Kategorie 1');
    }

    public function test_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/category/' . $this->categoryId);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldDisplayCategory() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/category/' . $this->categoryId);

        // then
        $response->assertOk();
        $response->assertSee('Kategorie 1');
    }

    public function test_shouldNotDisplayCategory_fromOtherCourseOfSameUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '');

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/category/' . $this->categoryId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }

    public function test_shouldNotDisplayCategory_fromOtherUser() {
        // given
        $otherKursId = $this->createKurs('Zweiter Kurs', '', false);
        $otherCategoryId = Category::create(['course_id' => $otherKursId, 'name' => 'Kategorie 2'])->id;

        // when
        $response = $this->get('/course/' . $otherKursId . '/admin/category/' . $otherCategoryId);

        // then
        $this->assertInstanceOf(ModelNotFoundException::class, $response->exception);
    }
}
